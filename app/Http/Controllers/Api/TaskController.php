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
    public function index(TaskPaginatedRequest $request)
    {
        $this->authorize('viewAny', Task::class);

        $pageIndex = $request->input('pageIndex');
        $pageSize = $request->input('pageSize');
        $search = $request->input('search', '');
        $filter = $request->input('filter', '');
        $council_id = auth()->user()->council->id;
        $cacheKey = "tasks:council_{$council_id}:page_{$pageIndex}:size_{$pageSize}:search_{$search}:filter_{$filter}";

        // Use Redis cache service
        return $this->successResponse(
            $this->cacheService->rememberPaginated($cacheKey, 3600, function () use ($request) {
                $tasks = $this->taskService->getAllTasks($request);
                return new TaskCollection($tasks);
            }, $pageIndex, $pageSize),
            'Tasks retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
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
    public function show(string $id)
    {
        $cacheKey = "task:{$id}";

        // Use Redis cache service with remember pattern
        return $this->successResponse(
            $this->cacheService->remember($cacheKey, 3600, function () use ($id) {
                $task = Task::findOrFail($id);
                $this->authorize('view', $task);
                return $this->taskService->getTaskById($id);
            }),
            'Task retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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
