<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TaskSubmission;
use App\Services\TaskSubmissionService;
use Illuminate\Http\Request;
use App\Http\Requests\TaskSubmissionRequests\TaskSubmissionPaginatedRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\CacheService;
use App\Http\Requests\TaskSubmissionRequests\CreateTaskSubmissionRequest;
use App\Http\Requests\TaskSubmissionRequests\UpdateTaskSubmissionRequest;
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

    /**
     * List Task Submissions
     *
     * Retrieve a paginated list of task submissions.
     *
     * @tags Task Submissions
     * @response 200 scenario="Success" {"status": "success", "message": "Submissions retrieved successfully", "data": []}
     */
    public function index(TaskSubmissionPaginatedRequest $request)
    {
        return $this->successResponse(
            new TaskSubmissionCollection($this->taskSubmissionService->getPaginatedSubmissions($request)),
            'Submissions retrieved successfully'
        );
    }

    /**
     * Create Task Submission
     *
     * Submit a task submission for review.
     *
     * @tags Task Submissions
     * @response 201 scenario="Created" {"status": "success", "message": "Submission created successfully", "data": {}}
     * @response 403 scenario="Forbidden" {"status": "error", "message": "Unauthorized"}
     */
    public function store(CreateTaskSubmissionRequest $request)
    {
        $this->authorize('create', TaskSubmission::class);
        $submission = $this->taskSubmissionService->createTaskSubmission($request->all());

        $this->cacheService->clearResourceCache('task-submissions');

        return $this->createdResponse($submission, 'Submission created successfully');
    }

    /**
     * Get Task Submission
     *
     * Retrieve a specific task submission by its ID.
     *
     * @tags Task Submissions
     * @response 200 scenario="Success" {"status": "success", "message": "Submission retrieved successfully", "data": {}}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
     */
    public function show(string $id)
    {
        $submission = TaskSubmission::findOrFail($id);
        $this->authorize('view', $submission);

        return $this->successResponse(
            $this->taskSubmissionService->getTaskSubmissionById($id),
            'Submission retrieved successfully'
        );
    }

    /**
     * Update Task Submission
     *
     * Update an existing task submission (e.g., change status or grade).
     *
     * @tags Task Submissions
     * @response 200 scenario="Success" {"status": "success", "message": "Submission updated successfully", "data": null}
     * @response 403 scenario="Forbidden" {"status": "error", "message": "Unauthorized"}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
     */
    public function update(UpdateTaskSubmissionRequest $request, string $id)
    {
        $submission = TaskSubmission::findOrFail($id);
        $this->authorize('update', $submission);
        $this->taskSubmissionService->updateTaskSubmission($id, $request->all());

        $this->cacheService->forget("task_submission:{$id}");
        $this->cacheService->clearResourceCache('task-submissions');

        return $this->successResponse(null, 'Submission updated successfully');
    }

    /**
     * Delete Task Submission
     *
     * Permanently delete a task submission by its ID.
     *
     * @tags Task Submissions
     * @response 200 scenario="Success" {"status": "success", "message": "Submission deleted successfully", "data": null}
     * @response 403 scenario="Forbidden" {"status": "error", "message": "Unauthorized"}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
     */
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
