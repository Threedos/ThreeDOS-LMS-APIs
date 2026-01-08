<?php

namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use App\Models\TaskSubmission;
use App\Services\TaskSubmissionService;
use Illuminate\Http\Request;
use App\Http\Requests\TaskSubmissionRequests\TaskSubmissionPaginatedRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\CacheService;

class TaskSubmissionController extends Controller
{
    use AuthorizesRequests;
    protected $taskSubmissionService;
    protected $cacheService;

    public function __construct(TaskSubmissionService $taskSubmissionService, CacheService $cacheService)
    {
        $this->taskSubmissionService = $taskSubmissionService;
        $this->cacheService = $cacheService;
    }

    /**
     * Display a listing of the resource.
     */
    public function GetAllTaskSubmissionsForUser(TaskSubmissionPaginatedRequest $taskSubmissionPaginatedRequest)
    {
        $this->authorize('viewOwn', TaskSubmission::class);

        $userId = $taskSubmissionPaginatedRequest->user()->id;
        $pageIndex = $taskSubmissionPaginatedRequest->pageIndex ?? 1;
        $pageSize = $taskSubmissionPaginatedRequest->pageSize ?? 10;

        $cacheKey = "task_submissions:user_{$userId}:page_{$pageIndex}:size_{$pageSize}";

        // Use Redis cache service
        return response()->json(
            $this->cacheService->remember($cacheKey, 3600, function () use ($taskSubmissionPaginatedRequest) {
                return $this->taskSubmissionService->getAllTaskSubmissionsForUser($taskSubmissionPaginatedRequest);
            })
        );
    }

    public function index(TaskSubmissionPaginatedRequest $taskSubmissionPaginatedRequest)
    {
        $this->authorize('viewAny', TaskSubmission::class);

        $councilId = $taskSubmissionPaginatedRequest->user()->council_id;
        $pageIndex = $taskSubmissionPaginatedRequest->pageIndex ?? 1;
        $pageSize = $taskSubmissionPaginatedRequest->pageSize ?? 10;

        $cacheKey = "task_submissions:council_{$councilId}:page_{$pageIndex}:size_{$pageSize}";

        // Use Redis cache service
        return response()->json(
            $this->cacheService->remember($cacheKey, 3600, function () use ($taskSubmissionPaginatedRequest) {
                return $this->taskSubmissionService->getAllTaskSubmissionsForCouncil($taskSubmissionPaginatedRequest);
            })
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', TaskSubmission::class);
        $submission = $this->taskSubmissionService->createTaskSubmission($request->all());

        // Clear task submission cache after creating
        $this->cacheService->clearResourceCache('task_submissions');
        $this->cacheService->clearResourceCache('task-submissions');

        return response()->json($submission, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cacheKey = "task_submission:{$id}";

        // Use Redis cache service
        return response()->json(
            $this->cacheService->remember($cacheKey, 3600, function () use ($id) {
                $submission = TaskSubmission::findOrFail($id);
                $this->authorize('view', $submission);
                return $this->taskSubmissionService->getTaskSubmissionById($id);
            })
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $submission = TaskSubmission::findOrFail($id);
        $this->authorize('update', $submission);
        $this->taskSubmissionService->updateTaskSubmission($id, $request->all());

        // Clear specific submission cache and submission list cache
        $this->cacheService->forget("task_submission:{$id}");
        $this->cacheService->clearResourceCache('task_submissions');
        $this->cacheService->clearResourceCache('task-submissions');

        return response()->json(['message' => 'TaskSubmission updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $submission = TaskSubmission::findOrFail($id);
        $this->authorize('delete', $submission);
        $this->taskSubmissionService->deleteTaskSubmission($id);

        // Clear specific submission cache and submission list cache
        $this->cacheService->forget("task_submission:{$id}");
        $this->cacheService->clearResourceCache('task_submissions');
        $this->cacheService->clearResourceCache('task-submissions');

        return response()->json(['message' => 'TaskSubmission deleted successfully']);
    }
}
