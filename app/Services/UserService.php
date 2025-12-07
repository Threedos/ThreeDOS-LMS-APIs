<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers()
    {
        return $this->userRepository->getAllUsers();
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
}
