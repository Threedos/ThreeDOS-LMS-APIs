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
        // Use Redis cache service
        return response()->json(
            $this->cacheService->remember('roles:all', 3600, function () {
                return $this->roleService->getAllRoles();
            })
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
            return response()->json($validator->errors(), 422);
        }
        $role = $this->roleService->createRole([
            "name" => $request->name,
        ]);

        // Clear role cache after creating
        $this->cacheService->clearResourceCache('roles');

        return response()->json($role, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Use Redis cache service
        return response()->json(
            $this->cacheService->remember('role:' . $id, 3600, function () use ($id) {
                return $this->roleService->getRoleById($id);
            })
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

        return response()->json(['message' => 'Role updated successfully']);
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

        return response()->json(['message' => 'Role deleted successfully']);
    }
}
