<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json($this->roleService->getAllRoles());
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
        return response()->json($role, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json($this->roleService->getRoleById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
        $this->roleService->updateRole($id, $request->all());
        return response()->json(['message' => 'Role updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $this->roleService->deleteRole($id);
        return response()->json(['message' => 'Role deleted successfully']);
    }
}
