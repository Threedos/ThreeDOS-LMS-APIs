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
use App\Models\Task;
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
        if ($request->role) {
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
     * Display specific charts and statistics for dashboard
     */
    public function dashboard()
    {
        return response()->json([
            'success' => true,
            'message' => 'Dashboard data retrieved successfully',
            // 'data' => $this->userService->getDashboardData()
        ]);
        $user = auth()->user();

        // Eager load relationships for the current user
        $user->load(['submissions', 'attendances']);

        $submissions = $user->submissions;

        // 1. GPA Calculation (Average of graded submissions)
        $gradedSubmissions = $submissions->where('status', 'graded');
        $totalGrades = $gradedSubmissions->sum('grade');
        $gradedCountTotal = $gradedSubmissions->count();
        $gpa = $gradedCountTotal > 0 ? round($totalGrades / $gradedCountTotal, 2) : 0;

        // 2. Grade Distribution
        $gradeDistribution = [
            'A' => $submissions->where('grade', '>=', 8)->count(),
            'B' => $submissions->whereBetween('grade', [7, 7.99])->count(),
            'C' => $submissions->whereBetween('grade', [6, 6.99])->count(),
            'D' => $submissions->whereBetween('grade', [5, 5.99])->count(),
            'F' => $submissions->where('grade', '<', 5)->count(),
        ];

        // 3. Attendance Statistics
        $attendances = $user->attendances;
        $totalClasses = $attendances->count();
        $presentCount = $attendances->where('status', 'present')->count();
        $absentCount = $attendances->where('status', 'absent')->count();
        $lateCount = $attendances->where('status', 'late')->count();
        $attendanceRate = $totalClasses > 0 ? round(($presentCount / $totalClasses) * 100, 2) : 0;

        // 4. Task Statistics (Improved Logic)
        // Get all tasks belonging to the user's council
        $councilId = $user->council_id;
        $totalCouncilTasksCount = Task::whereHas('council_session', function ($q) use ($councilId) {
            $q->where('council_id', $councilId);
        })->count();

        // Get count of unique tasks the user has submitted
        $submittedTaskIds = $submissions->pluck('task_id')->unique();
        $submittedCount = $submittedTaskIds->count();

        // Logic: Tasks Not Submitted = Total Council Tasks - Unique Submitted Tasks
        $notSubmittedCount = max(0, $totalCouncilTasksCount - $submittedCount);

        // Detailed submission status
        $gradedCount = $submissions->where('status', 'graded')->count();
        $pendingGradingCount = $submittedCount - $gradedCount;

        $taskCompletionRate = $totalCouncilTasksCount > 0
            ? round(($submittedCount / $totalCouncilTasksCount) * 100, 2)
            : 0;

        // 5. Recent Submissions (Last 5)
        $recentSubmissions = $submissions->sortByDesc('created_at')->take(5)->values();

        $dashboardData = [
            'gpa' => $gpa,
            'total_submissions' => $submissions->count(),
            'grade_distribution' => $gradeDistribution,
            'attendance_rate' => $attendanceRate,
            'total_classes' => $totalClasses,
            'present_count' => $presentCount,
            'absent_count' => $absentCount,
            'late_count' => $lateCount,
            'task_stats' => [
                'total_tasks' => $totalCouncilTasksCount,
                'submitted_tasks' => $submittedCount,
                'not_submitted_tasks' => $notSubmittedCount,
                'submitted_but_pending' => $pendingGradingCount,
                'graded_tasks' => $gradedCount,
                'completion_rate' => $taskCompletionRate,
            ],
            'recent_submissions' => $recentSubmissions,
            'last_active' => $user->last_active,
        ];

        return $this->successResponse($dashboardData, 'Dashboard data retrieved successfully');
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
