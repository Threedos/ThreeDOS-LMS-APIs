<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Http\Requests\PaginatedRequest;

class UserRepository implements UserRepositoryInterface
{
    public function getAllUsers($request)
    {
        $query=User::query();
        if($request->filter =='council'){
            $council_id = auth()->user()->council_id;
            $query->where('council_id', $council_id);
        }

        if($request->role){

            return $query->where('council_id', $council_id)->whereHas('role', function ($query) use ($request) {
                $query->where('name', $request->role);
            })->get();
        }
        
        return $query->all();
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
        if($request->filter =='council'){
            $council_id = auth()->user()->council_id;
            $query->where('council_id', $council_id);
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
