<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function getAllUsersPaginated(array $filters);
    public function getAllUsers(array $filters);
    public function getUserById($userId);
    public function createUser(array $userDetails);
    public function updateUser($userId, array $newDetails);
    public function deleteUser($userId);
    public function bulkCreateUsers($file);

}
