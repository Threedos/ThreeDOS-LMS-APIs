<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Http\Requests\PaginatedRequest;

class UserRepository implements UserRepositoryInterface
{
    public function getAllUsers(array $filters)
    {
        $query = User::query();

        if (isset($filters['council_id'])) {
            $query->where('council_id', $filters['council_id']);
        }

        if (!empty($filters['role'])) {
            return $query->whereHas('role', function ($query) use ($filters) {
                $query->where('name', $filters['role']);
            })->get();
        }

        return $query->get();
    }

    public function getAllUsersPaginated(array $filters)
    {
        $pageIndex = $filters['pageIndex'] ?? 1;
        $pageSize = $filters['pageSize'] ?? 10;
        $search = $filters['search'] ?? null;
        $sort = $filters['sort'] ?? null;
        $role_id = $filters['role_id'] ?? null;

        $query = User::query()->select('id', 'name', 'email', 'role_id', 'council_id');

        if (array_key_exists('council_id', $filters)) {
            $query->where('council_id', $filters['council_id']);
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($sort) {
            $query->orderBy($sort);
        }
        if ($role_id) {
            $query->where('role_id', $role_id);
        }
        $query->orderBy('created_at', 'desc');
        $query
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->orderByRaw("
        CASE roles.name
            WHEN 'VicePresident' THEN 4
            WHEN 'Head' THEN 3
            WHEN 'Instructor' THEN 2
            ELSE 1
        END DESC
    ")
            ->select('users.*');

        // Laravel pagination (LengthAwarePaginator)
        return $query->paginate($pageSize, ['*'], 'page', $pageIndex);
    }

    public function getUserById($userId)
    {

        return User::select('id', 'name', 'email', 'role_id', 'council_id')
            ->findOrFail($userId);
    }

    public function createUser(array $userDetails)
    {
        return User::create($userDetails);
    }

    public function updateUser($userId, array $newDetails)
    {
        return User::whereId($userId)->update($newDetails);
    }

    public function deleteUser($userId)
    {
        User::destroy($userId);
    }

    public function bulkCreateUsers($file)
    {
        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\UsersImport, $file);
    }

}
