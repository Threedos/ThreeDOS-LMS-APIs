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
    /**
     * List Sessions
     *
     * Retrieve a paginated list of council sessions for the authenticated user.
     *
     * @tags Council Sessions
     * @response 200 scenario="Success" {"status": "success", "message": "Sessions retrieved successfully", "data": []}
     */
    public function index(PaginatedSessionRequest $request)
    {
        $filters = [
            'council_id' => $request->user()->council_id,
            'role' => $request->user()->role->name,
            'search' => $request->search,
            'pageIndex' => $request->pageIndex,
            'pageSize' => $request->pageSize,
        ];

        return $this->successResponse(
            new SessionCollection($this->sessionService->getAllSessions($filters)),
            'Sessions retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Create Session
     *
     * Create a new council session.
     *
     * @tags Council Sessions
     * @response 201 scenario="Created" {"status": "success", "message": "Council session created successfully", "data": {}}
     */
    public function store(StoreSessionRequest $request)
    {
        $session = $this->sessionService->createSession($request->validated());

        // Clear session cache and dependent resource caches
        $this->cacheService->clearResourceCache('sessions');
        $this->cacheService->clearResourceCache('tasks');
        $this->cacheService->clearResourceCache('task-submissions');

        return $this->createdResponse($session, 'Council session created successfully');
    }

    /**
     * Display the specified resource.
     */
    /**
     * Get Session
     *
     * Retrieve a specific council session by its ID.
     *
     * @tags Council Sessions
     * @response 200 scenario="Success" {"status": "success", "message": "Session retrieved successfully", "data": {}}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
     */
    public function show(string $id)
    {
        $session = $this->sessionService->getSessionById($id);

        return $this->successResponse(
            $session,
            'Session retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update Session
     *
     * Update an existing council session.
     *
     * @tags Council Sessions
     * @response 200 scenario="Success" {"status": "success", "message": "Session updated successfully", "data": {}}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
     */
    public function update(UpdateSessionRequest $request, string $id)
    {
        $session = $this->sessionService->updateSession($id, $request->validated());

        // Clear specific session cache, session list cache and dependencies
        $this->cacheService->forget("session:{$id}");
        $this->cacheService->clearResourceCache('sessions');
        $this->cacheService->clearResourceCache('tasks');
        $this->cacheService->clearResourceCache('task-submissions');

        return $this->successResponse($session, 'Session updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Delete Session
     *
     * Permanently delete a council session by its ID.
     *
     * @tags Council Sessions
     * @response 204 scenario="No Content" {}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
     */
    public function destroy(string $id)
    {
        $this->sessionService->deleteSession($id);

        // Clear specific session cache, session list cache and dependencies
        $this->cacheService->forget("session:{$id}");
        $this->cacheService->clearResourceCache('sessions');
        $this->cacheService->clearResourceCache('tasks');
        $this->cacheService->clearResourceCache('task-submissions');

        return $this->noContentResponse('Session deleted successfully');
    }
}
