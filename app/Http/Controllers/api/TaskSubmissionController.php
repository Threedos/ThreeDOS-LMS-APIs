<?php

namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use App\Models\TaskSubmission;
use App\Services\TaskSubmissionService;
use Illuminate\Http\Request;
use App\Http\Requests\TaskSubmissionRequests\TaskSubmissionPaginatedRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskSubmissionController extends Controller
{
    use AuthorizesRequests;
    protected $taskSubmissionService;

    public function __construct(TaskSubmissionService $taskSubmissionService)
    {
        $this->taskSubmissionService = $taskSubmissionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function GetAllTaskSubmissionsForUser(TaskSubmissionPaginatedRequest $taskSubmissionPaginatedRequest)
    {
        $this->authorize('viewOwn', TaskSubmission::class);
        return response()->json($this->taskSubmissionService->getAllTaskSubmissionsForUser($taskSubmissionPaginatedRequest));
    }

    public function index(TaskSubmissionPaginatedRequest $taskSubmissionPaginatedRequest)
    {
        $this->authorize('viewAny', TaskSubmission::class);
        return response()->json($this->taskSubmissionService->getAllTaskSubmissionsForCouncil($taskSubmissionPaginatedRequest));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', TaskSubmission::class);
        $submission = $this->taskSubmissionService->createTaskSubmission($request->all());
        return response()->json($submission, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $submission = TaskSubmission::findOrFail($id);
        $this->authorize('view', $submission);
        return response()->json($this->taskSubmissionService->getTaskSubmissionById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $submission = TaskSubmission::findOrFail($id);
        $this->authorize('update', $submission);
        $this->taskSubmissionService->updateTaskSubmission($id, $request->all());
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
        return response()->json(['message' => 'TaskSubmission deleted successfully']);
    }
}
