<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function getAllUsersPaginated($request);
    public function getAllUsers($request);
    public function getUserById($userId);
    public function createUser(array $userDetails);
    public function updateUser($userId, array $newDetails);
    public function deleteUser($userId);
    public function bulkCreateUsers($file);

}
