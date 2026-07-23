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
     * List Attendances
     *
     * Retrieve a paginated list of attendance records.
     *
     * @tags Attendances
     * @response 200 scenario="Success" {"status": "success", "message": "Success", "data": []}
     */
    public function index(PaginatedAttendanceRequest $request)
    {
        $filters = $request->validated();
        $filters['user'] = $request->user();

        $attendances = $this->attendanceService->getAllAttendances($filters);

        return $this->successResponse(
            new AttendanceCollection($attendances),
            'Success'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Create Attendance
     *
     * Record an attendance entry for a user in a session.
     *
     * @tags Attendances
     * @response 201 scenario="Created" {"status": "success", "message": "Success", "data": {}}
     * @response 403 scenario="Forbidden" {"status": "error", "message": "Unauthorized"}
     */
    public function store(StoreAttendanceRequest $request)
    {
        $this->authorize('create', Attendance::class);
        $attendance = $this->attendanceService->createAttendance($request->validated());

        $this->cacheService->clearResourceCache('attendances');

        return $this->createdResponse($attendance, 'Success');
    }

    /**
     * Bulk Import Attendances
     *
     * Import multiple attendance records at once via an uploaded file.
     *
     * @tags Attendances
     * @response 200 scenario="Success" {"status": "success", "message": "Success", "data": null}
     * @response 403 scenario="Forbidden" {"status": "error", "message": "Unauthorized"}
     */
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
    /**
     * Get Attendance
     *
     * Retrieve a specific attendance record by its ID.
     *
     * @tags Attendances
     * @response 200 scenario="Success" {"status": "success", "message": "Success", "data": {}}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
     */
    public function show(string $id)
    {
        $attendance = $this->attendanceService->getAttendanceById($id);

        return $this->successResponse(
            $attendance,
            'Success'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update Attendance
     *
     * Update an existing attendance record.
     *
     * @tags Attendances
     * @response 200 scenario="Success" {"status": "success", "message": "Success", "data": {}}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
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
    /**
     * Delete Attendance
     *
     * Permanently delete an attendance record by its ID.
     *
     * @tags Attendances
     * @response 204 scenario="No Content" {}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
     */
    public function destroy(string $id)
    {
        $this->attendanceService->deleteAttendance($id);

        $this->cacheService->forget("attendance:{$id}");
        $this->cacheService->clearResourceCache('attendances');

        return $this->noContentResponse('Success');
    }
}
