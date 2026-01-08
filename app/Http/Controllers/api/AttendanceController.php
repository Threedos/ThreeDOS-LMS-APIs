<?php

namespace App\Http\Controllers\api;

use App\Http\Requests\AttendanceRequests\StoreAttendanceRequest;
use App\Http\Requests\AttendanceRequests\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Http\Resources\AttendanceResource;
use App\Http\Requests\AttendanceRequests\PaginatedAttendanceRequest;
use App\Http\Controllers\Controller;
use App\Services\CacheService;
class AttendanceController extends Controller
{
    protected $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(PaginatedAttendanceRequest $request)
    {
        $council_id = $request->user()->council_id;
        $pageIndex = $request->pageIndex;
        $pageSize = $request->pageSize;

        $cacheKey = "attendances:council_{$council_id}:page_{$pageIndex}:size_{$pageSize}";

        // Use Redis cache service
        $data = $this->cacheService->remember($cacheKey, 3600, function () use ($request, $council_id) {
            $baseQuery = Attendance::query();
            $baseQuery = $baseQuery->where('council_id', $council_id);
            return $baseQuery->paginate($request->pageSize, ['*'], 'pageIndex', $request->pageIndex);
        });

        return AttendanceResource::collection($data);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttendanceRequest $request)
    {
        //
        $attendance = Attendance::create($request->validated());

        // Clear attendance cache after creating
        $this->cacheService->clearResourceCache('attendances');

        return response()->json($attendance, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cacheKey = "attendance:{$id}";

        // Use Redis cache service
        return response()->json(
            $this->cacheService->remember($cacheKey, 3600, function () use ($id) {
                return Attendance::findOrFail($id);
            })
        );
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceRequest $request, string $id)
    {
        //
        $attendance = Attendance::findOrFail($id);
        $attendance->update($request->validated());

        // Clear specific attendance cache and attendance list cache
        $this->cacheService->forget("attendance:{$id}");
        $this->cacheService->clearResourceCache('attendances');

        return response()->json($attendance);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        // Clear specific attendance cache and attendance list cache
        $this->cacheService->forget("attendance:{$id}");
        $this->cacheService->clearResourceCache('attendances');

        return response()->json(null, 204);
    }
}
