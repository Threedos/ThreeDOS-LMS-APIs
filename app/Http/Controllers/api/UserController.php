<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\UserRequests\CreateUserRequest; 
use App\Http\Requests\UserRequests\UpdateUserRequest; 
use App\Http\Requests\PaginatedRequest;
class UserController extends Controller
{
    use AuthorizesRequests;
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(PaginatedRequest $request)
    {
        $this->authorize('viewAny', User::class);
        return response()->json($this->userService->getAllUsers($request));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserRequest $request)
    {
        // return response()->json($request->all());
        $this->authorize('create', User::class);
        $user = $this->userService->createUser($request->all());
        return response()->json('User created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('view', User::class);
        return response()->json($this->userService->getUserById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $this->authorize('update', User::class);
        $this->userService->updateUser($id, $request->all());
        return response()->json(['message' => 'User updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete', User::class);
        $this->userService->deleteUser($id);
        return response()->json(['message' => 'User deleted successfully']);
    }
}
