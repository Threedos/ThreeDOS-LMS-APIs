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
    /**
     * List Roles
     *
     * Retrieve all available roles.
     *
     * @tags Roles
     * @response 200 scenario="Success" {"status": "success", "message": "Roles retrieved successfully", "data": []}
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
    /**
     * Create Role
     *
     * Create a new user role.
     *
     * @tags Roles
     * @response 201 scenario="Created" {"status": "success", "message": "Role created successfully", "data": {}}
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
    /**
     * Get Role
     *
     * Retrieve a specific role by its ID.
     *
     * @tags Roles
     * @response 200 scenario="Success" {"status": "success", "message": "Role retrieved successfully", "data": {}}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
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
    /**
     * Update Role
     *
     * Update an existing role's name.
     *
     * @tags Roles
     * @response 200 scenario="Success" {"status": "success", "message": "Role updated successfully", "data": null}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
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
    /**
     * Delete Role
     *
     * Permanently delete a role by its ID.
     *
     * @tags Roles
     * @response 200 scenario="Success" {"status": "success", "message": "Role deleted successfully", "data": null}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
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
