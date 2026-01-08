<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class RateLimiting
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Multi-tier rate limiting
        $limits = [
            'minute' => ['limit' => $user->rate_limit ?? 60, 'window' => 60],
            'hour' => ['limit' => $user->hourly_limit ?? 1000, 'window' => 3600],
            'day' => ['limit' => $user->daily_limit ?? 10000, 'window' => 86400],
        ];

        foreach ($limits as $tier => $config) {
            $result = $this->checkLimit($user->id, $tier, $config['limit'], $config['window']);

            if (!$result['allowed']) {
                return $this->rateLimitResponse($result, $tier);
            }
        }

        $response = $next($request);

        // Add rate limit headers
        return $this->addRateLimitHeaders($response, $user->id, $limits);
    }

    private function checkLimit(string $userId, string $tier, int $limit, int $window): array
    {
        $key = "rate_limit:user:{$userId}:{$tier}";

        try {
            // Use Redis Lua script for atomic operations (prevents race conditions)
            $script = <<<LUA
                local key = KEYS[1]
                local limit = tonumber(ARGV[1])
                local window = tonumber(ARGV[2])
                local current = redis.call('GET', key)
                
                if current == false then
                    redis.call('SET', key, 1, 'EX', window)
                    return {1, limit - 1, window}
                end
                
                current = tonumber(current)
                if current >= limit then
                    local ttl = redis.call('TTL', key)
                    return {0, 0, ttl}
                end
                
                redis.call('INCR', key)
                local remaining = limit - current - 1
                local ttl = redis.call('TTL', key)
                return {1, remaining, ttl}
LUA;

            $result = Redis::eval($script, 1, $key, $limit, $window);

            return [
                'allowed' => (bool) $result[0],
                'remaining' => (int) $result[1],
                'retry_after' => (int) $result[2],
                'limit' => $limit,
            ];
        } catch (\Exception $e) {
            // Fallback to cache if Redis fails (or for testing)
            return $this->fallbackCheck($key, $limit, $window);
        }
    }

    private function fallbackCheck(string $key, int $limit, int $window): array
    {
        $attempts = Cache::get($key, 0);
        $timerKey = $key . ':timer';

        if ($attempts >= $limit) {
            $resetTime = Cache::get($timerKey, now()->addSeconds($window)->timestamp);
            $retryAfter = max(0, $resetTime - now()->timestamp);

            return [
                'allowed' => false,
                'remaining' => 0,
                'retry_after' => $retryAfter,
                'limit' => $limit,
            ];
        }

        if ($attempts === 0) {
            Cache::put($key, 1, $window);
            Cache::put($timerKey, now()->addSeconds($window)->timestamp, $window);
        } else {
            Cache::increment($key);
        }

        return [
            'allowed' => true,
            'remaining' => max(0, $limit - $attempts - 1),
            'retry_after' => 0,
            'limit' => $limit,
        ];
    }

    private function rateLimitResponse(array $result, string $tier): Response
    {
        return response()->json([
            'message' => 'Rate limit exceeded',
            'tier' => $tier,
            'limit' => $result['limit'],
            'retry_after' => $result['retry_after'],
        ], 429)
            ->header('X-RateLimit-Limit', $result['limit'])
            ->header('X-RateLimit-Remaining', 0)
            ->header('X-RateLimit-Reset', now()->addSeconds($result['retry_after'])->timestamp)
            ->header('Retry-After', $result['retry_after']);
    }

    private function addRateLimitHeaders(Response $response, string $userId, array $limits): Response
    {
        // Use minute tier for headers as it's the most granular
        $limit = $limits['minute']['limit'];
        $remaining = 0;
        $ttl = 0;

        $key = "rate_limit:user:{$userId}:minute";

        try {
            $attempts = Redis::get($key);
            if ($attempts === null) {
                $attempts = 0;
            }
            $ttl = Redis::ttl($key);
            if ($ttl < 0)
                $ttl = $limits['minute']['window'];
        } catch (\Exception $e) {
            $attempts = Cache::get($key, 0);
            $timerKey = $key . ':timer';
            $resetTime = Cache::get($timerKey);
            if ($resetTime) {
                $ttl = max(0, $resetTime - now()->timestamp);
            } else {
                $ttl = $limits['minute']['window'];
            }
        }

        $remaining = max(0, $limit - (int) $attempts);

        return $response
            ->header('X-RateLimit-Limit', $limit)
            ->header('X-RateLimit-Remaining', $remaining)
            ->header('X-RateLimit-Reset', now()->addSeconds($ttl)->timestamp);
    }
}