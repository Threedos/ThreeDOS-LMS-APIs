<?php

namespace App\Repositories;

use App\Interfaces\AttendanceRepositoryInterface;
use App\Models\Attendance;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AttendanceImport;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    public function getAllAttendances($request)
    {
        $council_id = $request->user()->council_id;
        $pageIndex = $request->pageIndex ?? 1;
        $pageSize = $request->pageSize ?? 20;

        $query = Attendance::query()
            ->whereHas('council_session', function ($q) use ($council_id) {
                $q->where('council_id', $council_id);
            })
            ->with(['council_session.council'])
            ->orderBy('created_at', 'desc');

        return $query->paginate($pageSize, ['*'], 'pageIndex', $pageIndex);
    }

    public function getAttendanceById($id)
    {
        return Attendance::findOrFail($id);
    }

    public function createAttendance(array $details)
    {
        return Attendance::create($details);
    }

    public function updateAttendance($id, array $details)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->update($details);
        return $attendance;
    }

    public function deleteAttendance($id)
    {
        $attendance = Attendance::findOrFail($id);
        return $attendance->delete();
    }

    public function bulkStoreAttendances($file)
    {
        return Excel::import(new AttendanceImport, $file);
    }
}
