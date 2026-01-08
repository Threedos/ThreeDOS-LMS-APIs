<?php

namespace App\Imports;


use Illuminate\Support\Collection;
use App\Models\User;
use App\Models\CouncilSession;
use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\ToCollection;

class AttendanceImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
        foreach ($collection as $row) {
            # code...
             // Skip if critical fields are missing
            if (!isset($row['email']) || !isset($row['name'])) {
                continue;
            }
            $userId= User::where('email', $row['email'])->first()->id;
            $sessionId= CouncilSession::where('name', $row['name'])->first()->id;
            Attendance::firstOrCreate([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'date' => $row['date'],
                'status' => $row['status'],
            ]);
        }
    }
}
