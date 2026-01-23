<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Http\Requests\PaginatedRequest;

class UserRepository implements UserRepositoryInterface
{
    public function getAllUsers($request)
    {
        if($request->role_id){
            return User::where('role_id', $request->role_id)->get();
        }
        return User::all();
    }
    public function getAllUsersPaginated($request)
    {
        $pageIndex = $request->pageIndex ?? 1;
        $pageSize = $request->pageSize ?? 10;
        $search = $request->search;
        $sort = $request->sort;
        $role_id = $request->role_id;
        $query = User::query()->select('id', 'name', 'email', 'role_id', 'council_id')
            // ->with('role', 'council')
        ;
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
