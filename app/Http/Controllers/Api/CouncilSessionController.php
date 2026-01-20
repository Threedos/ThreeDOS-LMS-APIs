<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SessionRequests\StoreSessionRequest;
use App\Http\Requests\SessionRequests\UpdateSessionRequest;
use App\Models\CouncilSession;
use App\Http\Resources\SessionResource;
use App\Http\Requests\SessionRequests\PaginatedSessionRequest;
use App\Http\Controllers\Controller;
use App\Services\CacheService;
use App\Models\Attendance;
use App\Http\Resources\SessionCollection;
class CouncilSessionController extends Controller
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
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

        // Use Redis cache service
        return $this->successResponse(
        $this->cacheService->remember($cacheKey, 3600, function () use ($request, $council_id) {
            $paginated = CouncilSession::where('council_id', $council_id)
                ->withCount('attendance')
                ->when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
                ->paginate($request->pageSize, ['*'], 'pageIndex', $request->pageIndex);

            return new SessionCollection($paginated);
        }),
        'Sessions retrieved successfully'
    );

    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSessionRequest $request)
    {
        //  
        $session = CouncilSession::create($request->validated());

        // Clear session cache after creating
        $this->cacheService->clearResourceCache('sessions');

        return $this->createdResponse($session, 'Council session created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cacheKey = "session:{$id}";

        // Use Redis cache service
        return $this->successResponse(
            $this->cacheService->remember($cacheKey, 3600, function () use ($id) {
                return CouncilSession::findOrFail($id)->withCount('attendance');
            }),
            'Session retrieved successfully'
        );

    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSessionRequest $request, string $id)
    {
        //
        $session = CouncilSession::findOrFail($id);
        $session->update($request->validated());

        // Clear specific session cache and session list cache
        $this->cacheService->forget("session:{$id}");
        $this->cacheService->clearResourceCache('sessions');

        return $this->successResponse($session, 'Session updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $session = CouncilSession::findOrFail($id);
        $session->delete();

        // Clear specific session cache and session list cache
        $this->cacheService->forget("session:{$id}");
        $this->cacheService->clearResourceCache('sessions');

        return $this->noContentResponse('Session deleted successfully');
    }
}
