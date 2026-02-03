<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\CacheService;

class RoleController extends Controller
{
    protected $roleService;
    protected $cacheService;

    public function __construct(RoleService $roleService, CacheService $cacheService)
    {
        $this->roleService = $roleService;
        $this->cacheService = $cacheService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->successResponse(
            $this->roleService->getAllRoles(),
            'Roles retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            "name" => "required",
        ]);
        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors(), 'Validation failed');
        }
        $role = $this->roleService->createRole([
            "name" => $request->name,
        ]);

        // Clear role cache after creating
        $this->cacheService->clearResourceCache('roles');

        return $this->createdResponse($role, 'Role created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->successResponse(
            $this->roleService->getRoleById($id),
            'Role retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->roleService->updateRole($id, $request->all());

        // Clear specific role cache and role list cache
        $this->cacheService->forget("role:{$id}");
        $this->cacheService->clearResourceCache('roles');

        return $this->successResponse(null, 'Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->roleService->deleteRole($id);

        // Clear specific role cache and role list cache
        $this->cacheService->forget("role:{$id}");
        $this->cacheService->clearResourceCache('roles');

        return $this->successResponse(null, 'Role deleted successfully');
    }
}
