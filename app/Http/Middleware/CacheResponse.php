<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Handle an incoming request and cache the response in Redis.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $ttl  Time to live in seconds (default: 3600 = 1 hour)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, int $ttl = 3600): Response
    {
        // Only cache GET requests
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        // Generate a unique cache key based on the request
        $cacheKey = $this->generateCacheKey($request);

        try {
            // Check if response exists in Redis
            $cachedResponse = Redis::get($cacheKey);

            if ($cachedResponse !== null) {
                $data = json_decode($cachedResponse, true);

                return response()->json($data)
                    ->header('X-Cache-Status', 'HIT')
                    ->header('X-Cache-Key', $cacheKey);
            }

            // Process the request
            $response = $next($request);

            // Only cache successful responses (2xx status codes)
            if ($response->isSuccessful() && $response instanceof \Illuminate\Http\JsonResponse) {
                $content = $response->getContent();

                // Store in Redis with TTL
                Redis::setex($cacheKey, $ttl, $content);

                $response->header('X-Cache-Status', 'MISS');
                $response->header('X-Cache-Key', $cacheKey);
            }

            return $response;

        } catch (\Exception $e) {
            // If Redis fails, just pass through without caching
            \Log::warning('Redis cache middleware failed: ' . $e->getMessage());
            return $next($request);
        }
    }

    /**
     * Generate a unique cache key for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    private function generateCacheKey(Request $request): string
    {
        $user = $request->user();
        $userId = $user ? $user->id : 'guest';

        // Include route, query parameters, and user ID in the cache key
        $uri = $request->getPathInfo();
        $queryParams = $request->query();
        ksort($queryParams); // Sort to ensure consistent keys

        $queryString = http_build_query($queryParams);

        return sprintf(
            'endpoint_cache:user:%s:uri:%s:query:%s',
            $userId,
            md5($uri),
            md5($queryString)
        );
    }
}
