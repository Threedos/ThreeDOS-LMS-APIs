<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Policies\TaskPolicy;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\TaskRequests\TaskPaginatedRequest;
use App\Http\Requests\TaskRequests\TaskStoreRequest;
use App\Services\CacheService;
use App\Http\Resources\TaskCollection;
use App\Http\Requests\TaskRequests\EditTaskRequest;

class TaskController extends Controller
{
    use AuthorizesRequests;
    protected $taskService;
    protected $cacheService;

    public function __construct(TaskService $taskService, CacheService $cacheService)
    {
        $this->taskService = $taskService;
        $this->cacheService = $cacheService;
    }

    /**
     * Display a listing of the resource.
     */
    /**
     * List Tasks
     *
     * Retrieve a paginated list of tasks for the authenticated user's council.
     *
     * @tags Tasks
     * @response 200 scenario="Success" {"status": "success", "message": "Tasks retrieved successfully", "data": []}
     */
    public function index(TaskPaginatedRequest $request)
    {
        $this->authorize('viewAny', Task::class);

        $tasks = $this->taskService->getAllTasks($request);

        return $this->successResponse(
            new TaskCollection($tasks),
            'Tasks retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Create Task
     *
     * Create a new task assigned to a council session.
     *
     * @tags Tasks
     * @response 201 scenario="Created" {"status": "success", "message": "Task created successfully", "data": {}}
     * @response 403 scenario="Forbidden" {"status": "error", "message": "Unauthorized"}
     */
    public function store(TaskStoreRequest $request)
    {
        $this->authorize('create', Task::class);
        $task = $this->taskService->createTask($request->all());

        // Clear task cache and submissions after creating
        $this->cacheService->clearResourceCache('tasks');
        $this->cacheService->clearResourceCache('task-submissions');

        return $this->createdResponse($task, 'Task created successfully');
    }

    /**
     * Display the specified resource.
     */
    /**
     * Get Task
     *
     * Retrieve a specific task by its ID.
     *
     * @tags Tasks
     * @response 200 scenario="Success" {"status": "success", "message": "Task retrieved successfully", "data": {}}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
     */
    public function show(string $id)
    {
        $task = Task::findOrFail($id);
        $this->authorize('view', $task);

        return $this->successResponse(
            $this->taskService->getTaskById($id),
            'Task retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update Task
     *
     * Update an existing task's details.
     *
     * @tags Tasks
     * @response 200 scenario="Success" {"status": "success", "message": "Task updated successfully", "data": null}
     * @response 403 scenario="Forbidden" {"status": "error", "message": "Unauthorized"}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
     */
    public function update(EditTaskRequest $request, string $id)
    {
        $task = Task::findOrFail($id);
        $this->authorize('update', $task);
        $this->taskService->updateTask($id, $request->all());

        // Clear specific task cache, task list cache and submissions
        $this->cacheService->forget("task:{$id}");
        $this->cacheService->clearResourceCache('tasks');
        $this->cacheService->clearResourceCache('task-submissions');

        return $this->successResponse(null, 'Task updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Delete Task
     *
     * Permanently delete a task by its ID.
     *
     * @tags Tasks
     * @response 200 scenario="Success" {"status": "success", "message": "Task deleted successfully", "data": null}
     * @response 403 scenario="Forbidden" {"status": "error", "message": "Unauthorized"}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        $this->authorize('delete', $task);
        $this->taskService->deleteTask($id);

        // Clear specific task cache, task list cache and submissions
        $this->cacheService->forget("task:{$id}");
        $this->cacheService->clearResourceCache('tasks');
        $this->cacheService->clearResourceCache('task-submissions');

        return $this->successResponse(null, 'Task deleted successfully');
    }
}
