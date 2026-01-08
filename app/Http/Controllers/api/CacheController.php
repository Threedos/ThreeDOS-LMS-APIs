<?php

namespace App\Http\Controllers\api;

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
     * Get cache statistics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        $stats = $this->cacheService->getStats();

        return response()->json([
            'status' => 'success',
            'data' => $stats,
        ]);
    }

    /**
     * Clear all endpoint cache.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearEndpointCache()
    {
        $deleted = $this->cacheService->clearAllEndpointCache();

        return response()->json([
            'status' => 'success',
            'message' => 'Endpoint cache cleared',
            'keys_deleted' => $deleted,
        ]);
    }

    /**
     * Clear cache for a specific resource.
     *
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

        return response()->json([
            'status' => 'success',
            'message' => "Cache cleared for resource: {$resource}",
            'keys_deleted' => $deleted,
        ]);
    }

    /**
     * Clear cache for a specific user.
     *
     * @param  string  $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearUserCache(string $userId)
    {
        $deleted = $this->cacheService->clearUserCache($userId);

        return response()->json([
            'status' => 'success',
            'message' => "Cache cleared for user: {$userId}",
            'keys_deleted' => $deleted,
        ]);
    }
}
