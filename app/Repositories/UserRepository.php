<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Http\Requests\PaginatedRequest;

class UserRepository implements UserRepositoryInterface
{
    public function getAllUsers($request)
    {
        $pageIndex = $request->pageIndex ?? 1;
        $pageSize = $request->pageSize ?? 10;
        $search = $request->search;
        $sort = $request->sort;
        $query = User::query()->select('id', 'name', 'email', 'role_id', 'council_id')
        // ->with('role', 'council')
        ;
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($sort) {
            $query->orderBy($sort);
        }
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
}
