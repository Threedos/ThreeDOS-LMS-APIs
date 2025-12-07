<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;

use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json($this->taskService->getAllTasks());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $task = $this->taskService->createTask($request->all());
        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json($this->taskService->getTaskById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->taskService->updateTask($id, $request->all());
        return response()->json(['message' => 'Task updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->taskService->deleteTask($id);
        return response()->json(['message' => 'Task deleted successfully']);
    }
}
