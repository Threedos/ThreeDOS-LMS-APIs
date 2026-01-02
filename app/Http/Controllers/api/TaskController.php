<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Policies\TaskPolicy;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\TaskRequests\TaskPaginatedRequest;
use Illuminate\Support\Facades\Cache;
class TaskController extends Controller
{
    use AuthorizesRequests;
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
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

        $cacheKey = "tasks:page_{$pageIndex}:size_{$pageSize}:search_{$search}:filter_{$filter}";

        $tasks = Cache::tags(['tasks'])->remember($cacheKey, 3600, function () use ($request) {
            return $this->taskService->getAllTasks($request);
        });

        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Task::class);
        $task = $this->taskService->createTask($request->all());
        Cache::tags(['tasks'])->flush();
        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::findOrFail($id);
        $this->authorize('view', $task);
        return response()->json($this->taskService->getTaskById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::findOrFail($id);
        $this->authorize('update', $task);
        $this->taskService->updateTask($id, $request->all());
        Cache::tags(['tasks'])->flush();
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
        Cache::tags(['tasks'])->flush();
        return response()->json(['message' => 'Task deleted successfully']);
    }
}
