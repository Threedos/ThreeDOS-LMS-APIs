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

    public function getAllUsers(PaginatedRequest $request)
    {
        return UserResource::collection($this->userRepository->getAllUsers($request));
    }

    public function getUserById($userId)
    {
        return UserResource::make($this->userRepository->getUserById($userId));
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
}
