<?php

namespace App\Services;

use App\Interfaces\RoleRepositoryInterface;

class RoleService
{
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getAllRoles()
    {
        return $this->roleRepository->getAllRoles();
    }

    public function getRoleById($roleId)
    {
        return $this->roleRepository->getRoleById($roleId);
    }

    public function createRole(array $roleDetails)
    {
        return $this->roleRepository->createRole($roleDetails);
    }

    public function updateRole($roleId, array $roleDetails)
    {
        return $this->roleRepository->updateRole($roleId, $roleDetails);
    }

    public function deleteRole($roleId)
    {
        return $this->roleRepository->deleteRole($roleId);
    }
}
