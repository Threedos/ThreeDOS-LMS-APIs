<?php

namespace App\Services;

use App\Enums\RolesEnum;
use App\Interfaces\UserRepositoryInterface;
use App\Http\Requests\PaginatedRequest;
use App\Http\Resources\UserResource;
class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsersPaginated($request)
    {
        $user = auth()->user();
        $filters = [
            'pageIndex' => $request->pageIndex ?? 1,
            'pageSize' => $request->pageSize ?? 10,
            'search' => $request->search,
            'sort' => $request->sort,
            'role_id' => $request->role_id,
        ];

        // Access control logic
        if ($request->filter == 'council') {
            $filters['council_id'] = $user->council_id;
        } elseif (in_array($user->role->name, ['Delegate', 'Instructor', 'Head'])) {
            $filters['council_id'] = $user->council_id;
        }
        // For VicePresident and President, we don't add council_id to $filters 
        // by default so they can see all users across all councils.
        // If they provided a council_id in the request, it's already in $filters from above.

        return $this->userRepository->getAllUsersPaginated($filters);
    }

    public function getAllUsers($request)
    {
        $user = auth()->user();
        $filters = [
            'role' => $request->role,
        ];

        if ($request->filter == 'council') {
            $filters['council_id'] = $user->council_id;
        }

        return $this->userRepository->getAllUsers($filters);
    }

    public function getUserById($userId)
    {
        return $this->userRepository->getUserById($userId);
    }

    public function createUser(array $userDetails)
    {
        return $this->userRepository->createUser($userDetails);
    }

    public function updateUser($userId, array $userDetails)
    {
        return $this->userRepository->updateUser($userId, $userDetails);
    }

    public function deleteUser($userId)
    {
        return $this->userRepository->deleteUser($userId);
    }

    public function bulkCreateUsers($file)
    {
        return $this->userRepository->bulkCreateUsers($file);
    }

    public function getDashboardData($request)
    {


        $authUser = auth()->user();
        $roleName = $authUser->role->name;

        if ($roleName == RolesEnum::Delegate->value) {
            $userId = $authUser->id;
        } else {
            $userId = $request->input('user_id');
        }

        return $this->userRepository->getDashboardData($userId);
    }

    
}
