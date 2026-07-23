<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequests\DashboardsRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\UserRequests\CreateUserRequest;
use App\Http\Requests\UserRequests\UpdateUserRequest;
use App\Http\Requests\UserRequests\BulkCreateUserRequest;
use App\Imports\UsersImport;
// use Maatwebsite\Excel\Facades\Excel;
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
    /**
     * Bulk Import Users
     *
     * Import multiple users at once via an uploaded file (CSV/XLSX).
     *
     * @tags Users
     * @response 200 scenario="Success" {"status": "success", "message": "Users imported successfully", "data": null}
     * @response 403 scenario="Forbidden" {"status": "error", "message": "Unauthorized"}
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
    /**
     * List Users
     *
     * Retrieve a paginated list of users. Filter by role using the `role` query parameter.
     *
     * @tags Users
     * @response 200 scenario="Paginated" {"status": "success", "message": "Users retrieved successfully", "data": {"data": [], "meta": {"current_page": 1, "per_page": 15, "total": 100}}}
     */
    public function index(PaginatedRequest $request)
    {
        $this->authorize('viewAny', User::class);

        if ($request->role) {
            return $this->successResponse(
                $this->userService->getAllUsers($request),
                'Users retrieved successfully'
            );
        }

        $usersPaginator = $this->userService->getAllUsersPaginated($request);
        $usersCollection = new UserCollection($usersPaginator);

        return $this->successResponse(
            $usersCollection->response()->getData(true),
            'Users retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Create User
     *
     * Create a new user account.
     *
     * @tags Users
     * @response 201 scenario="Created" {"status": "success", "message": "User created successfully", "data": null}
     * @response 403 scenario="Forbidden" {"status": "error", "message": "Unauthorized"}
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
    /**
     * Get User
     *
     * Retrieve a specific user by their ID.
     *
     * @tags Users
     * @response 200 scenario="Success" {"status": "success", "message": "User retrieved successfully", "data": {"id": 1, "name": "John Doe", "email": "john@example.com"}}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
     */
    public function show(string $id)
    {
        $userModel = $this->userService->getUserById($id);
        $this->authorize('view', $userModel);

        $resource = UserResource::make($userModel);

        return $this->successResponse(
            $resource->response()->getData(true),
            'User retrieved successfully'
        );
    }

    /**
     * Display specific charts and statistics for dashboard
     */
    /**
     * Dashboard Statistics
     *
     * Retrieve charts and statistics data for the dashboard.
     *
     * @tags Users
     * @response 200 scenario="Success" {"status": "success", "message": "Dashboard data retrieved successfully", "data": {}}
     */
    public function dashboard(DashboardsRequest $request)
    {
        $this->authorize('view', auth('api')->user());
        $dashboardData = $this->userService->getDashboardData($request);

        return $this->successResponse(
            $dashboardData,
            'Dashboard data retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update User
     *
     * Update the details of an existing user.
     *
     * @tags Users
     * @response 200 scenario="Success" {"status": "success", "message": "User updated successfully", "data": null}
     * @response 403 scenario="Forbidden" {"status": "error", "message": "Unauthorized"}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
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
    /**
     * Delete User
     *
     * Permanently delete a user by their ID.
     *
     * @tags Users
     * @response 200 scenario="Success" {"status": "success", "message": "User deleted successfully", "data": null}
     * @response 403 scenario="Forbidden" {"status": "error", "message": "Unauthorized"}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
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
