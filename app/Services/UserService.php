<?php

namespace App\Services;

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
        } elseif ($user->role->name == 'VicePresident') {
            $filters['council_id'] = null;
        }

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

}
