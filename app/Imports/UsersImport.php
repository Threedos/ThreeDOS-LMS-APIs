<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Council;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

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
            $roleId = null;
            if (isset($row['role'])) {
                $role = Role::where('name', $row['role'])->first();
                if ($role) {
                    $roleId = $role->id;
                }
            }   

            User::create([
                'name' => $row['name'],
                'email' => $row['email'],
                'password' => isset($row['password']) ? Hash::make($row['password']) : Hash::make('12345678'),
                'role_id' => $roleId,
                'council_id' => $councilId,
            ]);
        }
    }
}
