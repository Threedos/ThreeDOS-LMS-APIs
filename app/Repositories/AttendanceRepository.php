<?php

namespace App\Repositories;

use App\Interfaces\AttendanceRepositoryInterface;
use App\Models\Attendance;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AttendanceImport;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    public function getAllAttendances(array $filters)
    {
        $user = $filters['user'] ?? auth()->user();
        $role = $user->role->name;
        $council_id = $filters['council_id'] ?? $user->council_id; // Use target council if provided, else user's council

        $pageIndex = $filters['pageIndex'] ?? 1;
        $pageSize = $filters['pageSize'] ?? 20;

        $query = Attendance::query();

        // President / VicePresident can see everything if they don't provide council_id
        // Others are restricted to their council
        if (!in_array($role, ['VicePresident', 'President'])) {
            $query->whereHas('councilSession', function ($q) use ($council_id) {
                $q->where('council_id', $council_id);
            });
        } elseif (isset($filters['council_id'])) {
            $query->whereHas('councilSession', function ($q) use ($filters) {
                $q->where('council_id', $filters['council_id']);
            });
        }

        $query->with(['councilSession.council', 'user'])
            ->orderBy('created_at', 'desc');

        // Delegate → always restricted to their own attendance
        if ($role === 'Delegate') {
            $query->where('user_id', $user->id);
        } elseif (!empty($filters['user_id'])) {
            // Instructor / Head / VP can filter by a specific user_id
            $query->where('user_id', $filters['user_id']);
        }

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
