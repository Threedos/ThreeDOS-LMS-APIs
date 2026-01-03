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
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Cache;
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
        $pageIndex = $request->input('pageIndex');
        $pageSize = $request->input('pageSize');
        $search = $request->input('search', '');
        $cacheKey = "users:page_{$pageIndex}:size_{$pageSize}:search_{$search}";
        $users = Cache::tags(['users'])->remember($cacheKey, 3600, function () use ($request) {
            return UserResource::collection($this->userService->getAllUsers($request));
        });
        return $users;
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
        $cacheKey = "user:{$id}";
        if(Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        $userModel = $this->userService->getUserById($id);
        $this->authorize('view', $userModel);
        Cache::tags(['users'])->put($cacheKey, $userModel, 3600);
        return UserResource::make($userModel);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $userModel = $this->userService->getUserById($id);
        $this->authorize('update', $userModel);

        $this->userService->updateUser($id, $request->all());
        return response()->json(['message' => 'User updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $userModel = $this->userService->getUserById($id);
        $this->authorize('delete', $userModel);

        $this->userService->deleteUser($id);
        return response()->json(['message' => 'User deleted successfully']);
    }
}
