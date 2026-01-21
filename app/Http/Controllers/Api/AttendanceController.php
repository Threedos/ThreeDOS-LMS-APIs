<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AttendanceRequests\StoreAttendanceRequest;
use App\Http\Requests\AttendanceRequests\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Http\Resources\AttendanceResource;
use App\Http\Requests\AttendanceRequests\PaginatedAttendanceRequest;
use App\Http\Controllers\Controller;
use App\Services\CacheService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\AttendanceRequests\BulkCreateAttendanceRequest;
use App\Imports\AttendanceImport;
use App\Http\Resources\AttendanceCollection;
class AttendanceController extends Controller
{
    use AuthorizesRequests;
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
    $pageIndex = $request->pageIndex ?? 1;
    $pageSize = $request->pageSize ?? 20;

    // Cache key per council + page + size
    $cacheKey = "attendances:council_{$council_id}:page_{$pageIndex}:size_{$pageSize}";

    // Use Redis cache service
    $data = $this->cacheService->remember($cacheKey, 3600, function () use ($council_id, $pageIndex, $pageSize) {
        $query = Attendance::query()
            // Only attendances whose session belongs to this council
            ->whereHas('council_session', function ($q) use ($council_id) {
                $q->where('council_id', $council_id);
            })
            // Eager-load session and council for convenience
            ->with(['council_session.council']);

        // Paginate results
        return $query->paginate($pageSize, ['*'], 'pageIndex', $pageIndex);
    });

    return $this->successResponse(
        new AttendanceCollection($data),
        'Success'
    );
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttendanceRequest $request)
    {
        //
        $this->authorize('create', Attendance::class);
        $attendance = Attendance::create($request->validated());

        // Clear attendance cache after creating
        $this->cacheService->clearResourceCache('attendances');

        return $this->createdResponse($attendance, 'Success');
    }



    public function bulkStore(BulkCreateAttendanceRequest $request)
    {

        $this->authorize('create', Attendance::class);
        Excel::import(new AttendanceImport, $request->file('file'));

        // Clear attendance cache after bulk import
        $this->cacheService->clearResourceCache('attendances');

        return $this->successResponse(null, 'Success');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cacheKey = "attendance:{$id}";

        // Use Redis cache service
        return $this->successResponse(
            $this->cacheService->remember($cacheKey, 3600, function () use ($id) {
                return Attendance::findOrFail($id);
            }),
            'Success'
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

        return $this->successResponse($attendance, 'Success');
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

        return $this->noContentResponse('Success');
    }
}
