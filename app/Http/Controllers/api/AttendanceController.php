<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceRequests\StoreAttendanceRequest;
use App\Http\Requests\AttendanceRequests\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Http\Resources\AttendanceResource;
use App\Http\Requests\AttendanceRequests\PaginatedAttendanceRequest;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PaginatedAttendanceRequest $request)
    {
        //
        $council_id = $request->user()->council_id;
        $baseQuery= Attendance::query();
        $baseQuery= $baseQuery->where('council_id',$council_id);
        $attendance = $baseQuery->paginate($request->pageSize, ['*'], 'pageIndex', $request->pageIndex);
       
        return AttendanceResource::collection($attendance);
    }

   

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttendanceRequest $request)
    {
        //
        $attendance = Attendance::create($request->validated());
        return response()->json($attendance, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $attendance = Attendance::findOrFail($id);
        return response()->json($attendance);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceRequest $request, string $id)
    {
        //
        $attendance = Attendance::findOrFail($id);
        $attendance->update($request->validated());
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
        return response()->json(null, 204);
    }
}
