<?php

namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;

use App\Services\TaskSubmissionService;
use Illuminate\Http\Request;

class TaskSubmissionController extends Controller
{
    protected $taskSubmissionService;

    public function __construct(TaskSubmissionService $taskSubmissionService)
    {
        $this->taskSubmissionService = $taskSubmissionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json($this->taskSubmissionService->getAllTaskSubmissions());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $submission = $this->taskSubmissionService->createTaskSubmission($request->all());
        return response()->json($submission, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json($this->taskSubmissionService->getTaskSubmissionById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->taskSubmissionService->updateTaskSubmission($id, $request->all());
        return response()->json(['message' => 'TaskSubmission updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->taskSubmissionService->deleteTaskSubmission($id);
        return response()->json(['message' => 'TaskSubmission deleted successfully']);
    }
}
