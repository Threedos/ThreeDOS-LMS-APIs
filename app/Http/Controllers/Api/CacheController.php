<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CacheService;
use Illuminate\Http\Request;

class CacheController extends Controller
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Cache Statistics
     *
     * Get cache statistics including total keys, memory usage, and hit/miss rates.
     *
     * @tags Cache
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        $stats = $this->cacheService->getStats();

        return $this->successResponse($stats, 'Cache statistics retrieved successfully');
    }

    /**
     * Clear All Endpoint Cache
     *
     * Clear all cached endpoint responses.
     *
     * @tags Cache
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearEndpointCache()
    {
        $deleted = $this->cacheService->clearAllEndpointCache();

        return $this->successResponse(
            ['keys_deleted' => $deleted],
            'Endpoint cache cleared'
        );
    }

    /**
     * Clear Resource Cache
     *
     * Clear cached data for a specific resource type (users, councils, tasks, etc.).
     *
     * @tags Cache
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearResourceCache(Request $request)
    {
        $request->validate([
            'resource' => 'required|string|in:users,councils,tasks,sessions,attendances,roles,task-submissions',
        ]);

        $resource = $request->input('resource');
        $deleted = $this->cacheService->clearResourceCache($resource);

        return $this->successResponse(
            ['keys_deleted' => $deleted],
            "Cache cleared for resource: {$resource}"
        );
    }

    /**
     * Clear User Cache
     *
     * Clear all cached data associated with a specific user.
     *
     * @tags Cache
     * @param  string  $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearUserCache(string $userId)
    {
        $deleted = $this->cacheService->clearUserCache($userId);

        return $this->successResponse(
            ['keys_deleted' => $deleted],
            "Cache cleared for user: {$userId}"
        );
    }
}
