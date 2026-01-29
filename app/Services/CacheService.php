<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class CacheService
{
    /**
     * Store data in Redis cache.
     */
    public function put(string $key, $value, int $ttl = 3600): bool
    {
        try {
            $serialized = json_encode($value);
            return Redis::setex($key, $ttl, $serialized) === true;
        } catch (\Exception $e) {
            Log::error("Redis cache put failed for key {$key}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieve data from Redis cache.
     */
    public function get(string $key, $default = null)
    {
        try {
            $value = Redis::get($key);
            if ($value === null) return $default;
            return json_decode($value, true);
        } catch (\Exception $e) {
            Log::error("Redis cache get failed for key {$key}: " . $e->getMessage());
            return $default;
        }
    }

    /**
     * Remember a scalar or array value in cache.
     */
    public function remember(string $key, int $ttl, callable $callback)
    {
        $value = $this->get($key);
        if ($value !== null) return $value;

        $value = $callback();
        $this->put($key, $value, $ttl);
        return $value;
    }

    /**
     * Cache paginated data safely.
     *
     * @param string $key
     * @param int $ttl Seconds
     * @param callable $callback Returns LengthAwarePaginator
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return LengthAwarePaginator
     */
    public function rememberPaginated(string $key, int $ttl, callable $callback, int $page, int $perPage): LengthAwarePaginator
    {
        $cached = $this->get($key);

        if ($cached !== null) {
            return new LengthAwarePaginator(
                $cached['data'] ?? [],
                $cached['total'] ?? 0,
                $perPage,
                $page,
                [
                    'path' => request()->url(),
                    'query' => request()->query(),
                ]
            );
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $callback();

        // Cache only minimal array representation
        $this->put($key, [
            'data' => $paginator->items(),
            'total' => $paginator->total(),
        ], $ttl);

        return $paginator;
    }

    /**
     * Check if a key exists in Redis cache.
     */
    public function has(string $key): bool
    {
        try {
            return Redis::exists($key) > 0;
        } catch (\Exception $e) {
            Log::error("Redis cache has failed for key {$key}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove a key from Redis cache.
     */
    public function forget(string $key): bool
    {
        try {
            return Redis::del($key) > 0;
        } catch (\Exception $e) {
            Log::error("Redis cache forget failed for key {$key}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove all keys matching a pattern.
     */
    public function forgetByPattern(string $pattern): int
    {
        try {
            $keys = Redis::keys($pattern);
            if (empty($keys)) return 0;
            return Redis::del(...$keys);
        } catch (\Exception $e) {
            Log::error("Redis cache forgetByPattern failed for pattern {$pattern}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Clear all user-related cache.
     */
    public function clearUserCache(?string $userId = null): int
    {
        $patterns = $userId
            ? ["user:{$userId}", "user:{$userId}:*", "endpoint_cache:user:{$userId}:*"]
            : ["user:*", "endpoint_cache:*"];

        $totalDeleted = 0;
        foreach ($patterns as $pattern) {
            $totalDeleted += $this->forgetByPattern($pattern);
        }

        return $totalDeleted;
    }

    /**
     * Clear all endpoint cache for a resource.
     */
    public function clearResourceCache(string $resource): int
    {
        $resourceUnderscore = str_replace('-', '_', $resource);
        $resourceHyphen = str_replace('_', '-', $resource);

        $patterns = [
            "endpoint_cache:*:uri:*{$resource}*",
            "endpoint_cache:*:uri:*{$resourceUnderscore}*",
            "endpoint_cache:*:uri:*{$resourceHyphen}*",
            "{$resourceUnderscore}:*",
            "{$resourceHyphen}:*",
            rtrim($resourceUnderscore, 's') . ":*",
            rtrim($resourceHyphen, 's') . ":*",
        ];

        $totalDeleted = 0;
        foreach ($patterns as $pattern) {
            $totalDeleted += $this->forgetByPattern($pattern);
        }

        return $totalDeleted;
    }

    /**
     * Clear all endpoint cache.
     */
    public function clearAllEndpointCache(): int
    {
        return $this->forgetByPattern('endpoint_cache:*');
    }

    /**
     * Get Redis cache statistics.
     */
    public function getStats(): array
    {
        try {
            $info = Redis::info();

            return [
                'used_memory' => $info['used_memory_human'] ?? 'N/A',
                'connected_clients' => $info['connected_clients'] ?? 'N/A',
                'total_keys' => $this->countKeys(),
                'endpoint_cache_keys' => $this->countKeys('endpoint_cache:*'),
                'user_cache_keys' => $this->countKeys('user:*'),
                'rate_limit_keys' => $this->countKeys('rate_limit:*'),
            ];
        } catch (\Exception $e) {
            Log::error('Redis cache stats failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Count keys matching a pattern.
     */
    private function countKeys(string $pattern = '*'): int
    {
        try {
            $keys = Redis::keys($pattern);
            return count($keys);
        } catch (\Exception $e) {
            return 0;
        }
    }
}
