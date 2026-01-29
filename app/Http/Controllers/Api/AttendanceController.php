<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AttendanceRequests\StoreAttendanceRequest;
use App\Http\Requests\AttendanceRequests\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Http\Requests\AttendanceRequests\PaginatedAttendanceRequest;
use App\Http\Controllers\Controller;
use App\Services\CacheService;
use App\Services\AttendanceService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\AttendanceRequests\BulkCreateAttendanceRequest;
use App\Http\Resources\AttendanceCollection;

class AttendanceController extends Controller
{
    use AuthorizesRequests;
    protected $cacheService;
    protected $attendanceService;

    public function __construct(CacheService $cacheService, AttendanceService $attendanceService)
    {
        $this->cacheService = $cacheService;
        $this->attendanceService = $attendanceService;
    }

    /**
     * Display a listing of the resource.
     */
    // public function index(PaginatedAttendanceRequest $request)
    // {
    //     $pageIndex = $request->pageIndex ?? 1;
    //     $pageSize = $request->pageSize ?? 20;
    //     $user_id = $request->user_id;
    //     $target_council_id = $request->council_id;

    //     $cacheKey = "attendances:u_{$request->user()->id}:p_{$pageIndex}:s_{$pageSize}:target_u_{$user_id}:target_c_{$target_council_id}";

    //     $data = $this->cacheService->remember($cacheKey, 3600, function () use ($request) {
    //         $filters = $request->validated();
    //         $filters['user'] = $request->user();
    //         return $this->attendanceService->getAllAttendances($filters);
    //     });

    //     return $this->successResponse(
    //         new AttendanceCollection($data),
    //         'Success'
    //     );
    // }
public function index(PaginatedAttendanceRequest $request)
{
    $pageIndex = $request->pageIndex ?? 1;
    $pageSize = $request->pageSize ?? 20;

    $filters = $request->validated();
    $filters['user'] = $request->user();

    $cacheKey = "attendances:u_{$request->user()->id}:p_{$pageIndex}:s_{$pageSize}";

    $attendances = $this->cacheService->rememberPaginated(
        $cacheKey,
        3600,
        fn() => $this->attendanceService->getAllAttendances($filters),
        $pageIndex,
        $pageSize
    );

    return $this->successResponse(
        new AttendanceCollection($attendances),
        'Success'
    );
}

// public function index(PaginatedAttendanceRequest $request)
// {
//     $filters = $request->validated();
//     $filters['user'] = $request->user();

//     $attendances = $this->attendanceService->getAllAttendances($filters);

//     return $this->successResponse(
//         new AttendanceCollection($attendances),
//         'Success'
//     );
// }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttendanceRequest $request)
    {
        $this->authorize('create', Attendance::class);
        $attendance = $this->attendanceService->createAttendance($request->validated());

        $this->cacheService->clearResourceCache('attendances');

        return $this->createdResponse($attendance, 'Success');
    }

    public function bulkStore(BulkCreateAttendanceRequest $request)
    {
        $this->authorize('create', Attendance::class);
        $this->attendanceService->bulkStoreAttendances($request->file('file'));

        $this->cacheService->clearResourceCache('attendances');

        return $this->successResponse(null, 'Success');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cacheKey = "attendance:{$id}";

        return $this->successResponse(
            $this->cacheService->remember($cacheKey, 3600, function () use ($id) {
                return $this->attendanceService->getAttendanceById($id);
            }),
            'Success'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceRequest $request, string $id)
    {
        $attendance = $this->attendanceService->updateAttendance($id, $request->validated());

        $this->cacheService->forget("attendance:{$id}");
        $this->cacheService->clearResourceCache('attendances');

        return $this->successResponse($attendance, 'Success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->attendanceService->deleteAttendance($id);

        $this->cacheService->forget("attendance:{$id}");
        $this->cacheService->clearResourceCache('attendances');

        return $this->noContentResponse('Success');
    }
}
