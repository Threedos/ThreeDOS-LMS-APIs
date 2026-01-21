<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\Models\User;
use App\Models\CouncilSession;
use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AttendanceImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            // Skip if critical fields are missing
            if (!isset($row['email']) || !isset($row['title'])) {
                continue;
            }

            $user = User::where('email', $row['email'])->first();
            $session = CouncilSession::where('title', $row['title'])->first();

            if ($user && $session) {
                Attendance::firstOrCreate([
                    'user_id' => $user->id,
                    'council_session_id' => $session->id,
                    'date' => $row['date'] ?? now(),
                    'status' => $row['status'] ?? 'present',
                ]);
            }
        }
    }
}
