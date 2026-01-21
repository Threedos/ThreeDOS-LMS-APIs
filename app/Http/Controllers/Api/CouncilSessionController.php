<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SessionRequests\StoreSessionRequest;
use App\Http\Requests\SessionRequests\UpdateSessionRequest;
use App\Http\Requests\SessionRequests\PaginatedSessionRequest;
use App\Http\Controllers\Controller;
use App\Services\CacheService;
use App\Services\CouncilSessionService;
use App\Http\Resources\SessionCollection;

class CouncilSessionController extends Controller
{
    protected $cacheService;
    protected $sessionService;

    public function __construct(CacheService $cacheService, CouncilSessionService $sessionService)
    {
        $this->cacheService = $cacheService;
        $this->sessionService = $sessionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(PaginatedSessionRequest $request)
    {
        $council_id = $request->user()->council_id;
        $pageIndex = $request->pageIndex;
        $pageSize = $request->pageSize;
        $search = $request->search ?? '';

        $cacheKey = "sessions:council_{$council_id}:page_{$pageIndex}:size_{$pageSize}:search_{$search}";

        return $this->successResponse(
            $this->cacheService->remember($cacheKey, 3600, function () use ($request) {
                return new SessionCollection($this->sessionService->getAllSessions($request));
            }),
            'Sessions retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSessionRequest $request)
    {
        $session = $this->sessionService->createSession($request->validated());

        $this->cacheService->clearResourceCache('sessions');

        return $this->createdResponse($session, 'Council session created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cacheKey = "session:{$id}";

        return $this->successResponse(
            $this->cacheService->remember($cacheKey, 3600, function () use ($id) {
                return $this->sessionService->getSessionById($id);
            }),
            'Session retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSessionRequest $request, string $id)
    {
        $session = $this->sessionService->updateSession($id, $request->validated());

        $this->cacheService->forget("session:{$id}");
        $this->cacheService->clearResourceCache('sessions');

        return $this->successResponse($session, 'Session updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->sessionService->deleteSession($id);

        $this->cacheService->forget("session:{$id}");
        $this->cacheService->clearResourceCache('sessions');

        return $this->noContentResponse('Session deleted successfully');
    }
}
