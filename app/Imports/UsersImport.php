<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Council;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;

class UsersImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip if critical fields are missing
            if (!isset($row['email']) || !isset($row['name'])) {
                continue;
            }

            $councilId = null;
            if (isset($row['council'])) {
                $council = Council::where('name', $row['council'])->first();
                if ($council) {
                    $councilId = $council->id;
                }
            }

            User::create([
                'name' => $row['name'],
                'email' => $row['email'],
                'password' => isset($row['password']) ? Hash::make($row['password']) : Hash::make('12345678'),
                'role_id' => $row['role_id'] ?? null,
                'council_id' => $councilId,
            ]);
        }
    }
}
