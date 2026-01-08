<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RateLimiting
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Configuration
        $limit = $user->rate_limit ?? 6;   // requests
        $window = 60;                       // seconds

        $key = "rate_limit:user:{$user->id}";

        // Get current attempts
        $attempts = Cache::get($key, 0);

        if ($attempts >= $limit) {
            $retryAfter = Cache::getRedis()->ttl($key);

            return response()->json([
                'message' => 'Rate limit exceeded',
                'retry_after' => $retryAfter,
            ], 429);
        }

        // Atomic increment
        Cache::add($key, 0, $window); // set TTL only once
        Cache::increment($key);

        return $next($request);
    }
}
