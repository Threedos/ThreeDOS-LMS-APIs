<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\UserRequests\CreateUserRequest;
use App\Http\Requests\UserRequests\UpdateUserRequest;
use App\Http\Requests\UserRequests\BulkCreateUserRequest;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginatedRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use Illuminate\Support\Facades\Cache;
use App\Services\CacheService;
class UserController extends Controller
{
    use AuthorizesRequests;
    protected $userService;
    protected $cacheService;

    public function __construct(UserService $userService, CacheService $cacheService)
    {
        $this->userService = $userService;
        $this->cacheService = $cacheService;
    }

    /**
     * Store a newly created resource in storage in bulk.
     */
    public function BulkStore(BulkCreateUserRequest $request)
    {
        $this->authorize('create', User::class);
        $this->userService->bulkCreateUsers($request->file('file'));


        // Clear all user cache after bulk import
        $this->cacheService->clearResourceCache('users');

        return $this->successResponse(null, 'Users imported successfully');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(PaginatedRequest $request)
    {
        $this->authorize('viewAny', User::class);

        $pageIndex = $request->input('pageIndex', 1);
        $pageSize = $request->input('pageSize', 10);
        $search = $request->input('search', '');
        $cacheKey = "users:page_{$pageIndex}:size_{$pageSize}:search_{$search}";
        if($request->role_id){
            return $this->successResponse(
                $this->cacheService->remember($cacheKey, 3600, function () use ($request) {
                    return $this->userService->getAllUsers($request);
                }),
                'Users retrieved successfully'
            );
        }

        // Use Redis cache service
        return $this->successResponse(
            $this->cacheService->remember($cacheKey, 3600, function () use ($request) {
                $usersPaginator = $this->userService->getAllUsersPaginated($request);
                $usersCollection = new UserCollection($usersPaginator);
                return $usersCollection->response()->getData(true);
            }),
            'Users retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserRequest $request)
    {
        $this->authorize('create', User::class);
        $user = $this->userService->createUser($request->all());

        // Clear user list cache after creating a new user
        $this->cacheService->clearResourceCache('users');

        return $this->createdResponse(null, 'User created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cacheKey = "user:{$id}";

        // Use Redis cache service with remember pattern
        return $this->successResponse(
            $this->cacheService->remember($cacheKey, 3600, function () use ($id) {
                $userModel = $this->userService->getUserById($id);
                $this->authorize('view', $userModel);
                $resource = UserResource::make($userModel);
                return $resource->response()->getData(true);
            }),
            'User retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $userModel = $this->userService->getUserById($id);
        $this->authorize('update', $userModel);

        $this->userService->updateUser($id, $request->all());

        // Clear specific user cache and user list cache
        $this->cacheService->forget("user:{$id}");
        $this->cacheService->clearResourceCache('users');

        return $this->successResponse(null, 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $userModel = $this->userService->getUserById($id);
        $this->authorize('delete', $userModel);

        $this->userService->deleteUser($id);

        // Clear specific user cache and user list cache
        $this->cacheService->forget("user:{$id}");
        $this->cacheService->clearResourceCache('users');

        return $this->successResponse(null, 'User deleted successfully');
    }
}
