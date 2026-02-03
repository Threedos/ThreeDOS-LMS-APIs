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
        return $this->successResponse(
            new TaskSubmissionCollection($this->taskSubmissionService->getPaginatedSubmissions($request)),
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
        $submission = TaskSubmission::findOrFail($id);
        $this->authorize('view', $submission);

        return $this->successResponse(
            $this->taskSubmissionService->getTaskSubmissionById($id),
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
