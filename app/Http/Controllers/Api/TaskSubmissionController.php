<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TaskSubmission;
use App\Services\TaskSubmissionService;
use Illuminate\Http\Request;
use App\Http\Requests\TaskSubmissionRequests\TaskSubmissionPaginatedRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\CacheService;
use App\Http\Requests\taskSubmissionRequests\CreateTaskSubmissionRequest;
use App\Http\Resources\TaskSubmissionCollection;

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

    public function index(TaskSubmissionPaginatedRequest $request)
    {
        $user = $request->user();
        $scope = ($user->role->name == 'Instructor' || $user->role->name == 'Head') ? 'council' : 'user';
        $scopeId = ($scope == 'council') ? $user->council_id : $user->id;

        $pageIndex = $request->input('pageIndex', 1);
        $pageSize = $request->input('pageSize', 10);
        $search = $request->input('search', '');
        $filter = $request->input('filter', '');
        $sort = $request->input('sort', '');
        $task_id = $request->input('task_id', '');
        $user_id = $request->input('user_id', '');
        $status = $request->input('status', '');

        $cacheKey = "task_submissions:{$scope}_{$scopeId}:page_{$pageIndex}:size_{$pageSize}:search_{$search}:filter_{$filter}:sort_{$sort}:task_{$task_id}:user_{$user_id}:status_{$status}";

        return $this->successResponse(
            $this->cacheService->remember($cacheKey, 3600, function () use ($request) {
                return new TaskSubmissionCollection($this->taskSubmissionService->getPaginatedSubmissions($request));
            }),
            'Submissions retrieved successfully'
        );
    }

    public function store(CreateTaskSubmissionRequest $request)
    {
        $this->authorize('create', TaskSubmission::class);
        $submission = $this->taskSubmissionService->createTaskSubmission($request->all());

        $this->cacheService->clearResourceCache('task-submissions');

        return $this->createdResponse($submission, 'Submission created successfully');
    }

    public function show(string $id)
    {
        $cacheKey = "task_submission:{$id}";

        return $this->successResponse(
            $this->cacheService->remember($cacheKey, 60, function () use ($id) {
                $submission = TaskSubmission::findOrFail($id);
                $this->authorize('view', $submission);
                return $this->taskSubmissionService->getTaskSubmissionById($id);
            }),
            'Submission retrieved successfully'
        );
    }

    public function update(Request $request, string $id)
    {
        $submission = TaskSubmission::findOrFail($id);
        $this->authorize('update', $submission);
        $this->taskSubmissionService->updateTaskSubmission($id, $request->all());

        $this->cacheService->forget("task_submission:{$id}");
        $this->cacheService->clearResourceCache('task-submissions');

        return $this->successResponse(null, 'Submission updated successfully');
    }

    public function destroy(string $id)
    {
        $submission = TaskSubmission::findOrFail($id);
        $this->authorize('delete', $submission);
        $this->taskSubmissionService->deleteTaskSubmission($id);

        $this->cacheService->forget("task_submission:{$id}");
        $this->cacheService->clearResourceCache('task-submissions');

        return $this->successResponse(null, 'Submission deleted successfully');
    }
}
