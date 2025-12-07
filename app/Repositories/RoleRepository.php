<?php

namespace App\Repositories;

use App\Interfaces\RoleRepositoryInterface;
use App\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{
    public function getAllRoles()
    {
        return Role::all();
    }

    public function getRoleById($roleId)
    {
        return Role::findOrFail($roleId);
    }

    public function createRole(array $roleDetails)
    {
        return Role::create($roleDetails);
    }

    public function updateRole($roleId, array $newDetails)
    {
        return Role::whereId($roleId)->update($newDetails);
    }

    public function deleteRole($roleId)
    {
        Role::destroy($roleId);
    }
}
