<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * Store data in Redis cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $ttl  Time to live in seconds
     * @return bool
     */
    public function put(string $key, $value, int $ttl = 3600): bool
    {
        try {
            $serialized = json_encode($value);
            return Redis::setex($key, $ttl, $serialized) === true;
        } catch (\Exception $e) {
            Log::error('Redis cache put failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieve data from Redis cache.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        try {
            $value = Redis::get($key);

            if ($value === null) {
                return $default;
            }

            return json_decode($value, true);
        } catch (\Exception $e) {
            Log::error('Redis cache get failed: ' . $e->getMessage());
            return $default;
        }
    }

    /**
     * Check if a key exists in Redis cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function has(string $key): bool
    {
        try {
            return Redis::exists($key) > 0;
        } catch (\Exception $e) {
            Log::error('Redis cache has failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove a key from Redis cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        try {
            return Redis::del($key) > 0;
        } catch (\Exception $e) {
            Log::error('Redis cache forget failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear all cache keys matching a pattern.
     *
     * @param  string  $pattern
     * @return int  Number of keys deleted
     */
    public function forgetByPattern(string $pattern): int
    {
        try {
            $keys = Redis::keys($pattern);

            if (empty($keys)) {
                return 0;
            }

            return Redis::del(...$keys);
        } catch (\Exception $e) {
            Log::error('Redis cache forgetByPattern failed: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Clear all user-related cache.
     *
     * @param  string|null  $userId
     * @return int
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
     * Clear all endpoint cache for a specific resource.
     *
     * @param  string  $resource  e.g., 'users', 'councils', 'tasks'
     * @return int
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
     *
     * @return int
     */
    public function clearAllEndpointCache(): int
    {
        return $this->forgetByPattern('endpoint_cache:*');
    }

    /**
     * Get cache statistics.
     *
     * @return array
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
     *
     * @param  string  $pattern
     * @return int
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

    /**
     * Remember a value in cache (get or put).
     *
     * @param  string  $key
     * @param  int  $ttl
     * @param  callable  $callback
     * @return mixed
     */
    public function remember(string $key, int $ttl, callable $callback)
    {
        $value = $this->get($key);

        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        $this->put($key, $value, $ttl);

        return $value;
    }
}
