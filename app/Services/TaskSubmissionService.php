<?php

namespace App\Services;

use App\Interfaces\TaskSubmissionRepositoryInterface;
use App\Http\Requests\TaskSubmissionRequests\TaskSubmissionPaginatedRequest;
use App\Notifications\EventNotification;
use App\Enums\TaskStatusEnum;

class TaskSubmissionService
{
    protected $taskSubmissionRepository;

    public function __construct(TaskSubmissionRepositoryInterface $taskSubmissionRepository)
    {
        $this->taskSubmissionRepository = $taskSubmissionRepository;
    }

    public function getAllTaskSubmissionsForUser(TaskSubmissionPaginatedRequest $taskSubmissionPaginatedRequest)
    {
        return $this->taskSubmissionRepository->getAllTaskSubmissionsForUser($taskSubmissionPaginatedRequest->all());
    }

    public function getAllTaskSubmissionsForCouncil(TaskSubmissionPaginatedRequest $taskSubmissionPaginatedRequest)
    {
        return $this->taskSubmissionRepository->getAllTaskSubmissionsForCouncil($taskSubmissionPaginatedRequest->all());
    }

    public function getTaskSubmissionById($submissionId)
    {
        return $this->taskSubmissionRepository->getTaskSubmissionById($submissionId);
    }

    public function createTaskSubmission(array $submissionDetails)
    {
        $userId=auth()->user()->id;
        $councilId=auth()->user()->council_id;
        $data=[
            'user_id'=>$userId,
            'council_id'=>$councilId,
            'task_id'=>$submissionDetails['task_id'],
            'file'=>$submissionDetails['file'],
            'status'=>TaskStatusEnum::SUBMITTED->value,
        ];
        return $this->taskSubmissionRepository->createTaskSubmission($data);
    }

    public function updateTaskSubmission($submissionId, array $submissionDetails)
    {
        $submission = $this->taskSubmissionRepository
            ->updateTaskSubmission($submissionId, $submissionDetails);
        if($submissionDetails['status'] == 'graded') {
        // Resolve recipient correctly
        $delegate = $submission->user;

        // Notify the instructor
        $delegate->notify(
            new EventNotification(
                'Task Updated',
                'A submission was updated',
                [
                   'task' => $submission->task->name,
                ]
            )
        );
    }

return $submission;
    }

    public function deleteTaskSubmission($submissionId)
    {
        return $this->taskSubmissionRepository->deleteTaskSubmission($submissionId);
    }
}
