<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Policies\TaskPolicy;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\TaskRequests\TaskPaginatedRequest;
use App\Services\CacheService;
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
        return response()->json(
            $this->cacheService->remember($cacheKey, 3600, function () use ($request) {
                return $this->taskService->getAllTasks($request);
            })
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Task::class);
        $task = $this->taskService->createTask($request->all());

        // Clear task cache after creating
        $this->cacheService->clearResourceCache('tasks');

        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cacheKey = "task:{$id}";

        // Use Redis cache service with remember pattern
        return response()->json(
            $this->cacheService->remember($cacheKey, 3600, function () use ($id) {
                $task = Task::findOrFail($id);
                $this->authorize('view', $task);
                return $this->taskService->getTaskById($id);
            })
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

        // Clear specific task cache and task list cache
        $this->cacheService->forget("task:{$id}");
        $this->cacheService->clearResourceCache('tasks');

        return response()->json(['message' => 'Task updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        $this->authorize('delete', $task);
        $this->taskService->deleteTask($id);

        // Clear specific task cache and task list cache
        $this->cacheService->forget("task:{$id}");
        $this->cacheService->clearResourceCache('tasks');

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
