<?php

namespace App\Services;

use App\Interfaces\TaskSubmissionRepositoryInterface;
use App\Http\Requests\TaskSubmissionRequests\TaskSubmissionPaginatedRequest;
use App\Notifications\EventNotification;
use App\Enums\TaskStatusEnum;
use Illuminate\Support\Facades\Storage;

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
        $user = auth()->user();

        if (!($submissionDetails['file'] instanceof \Illuminate\Http\UploadedFile)) {
            throw new \InvalidArgumentException('Invalid file upload');
        }

        $filePath = Storage::disk('s3')
            ->putFile('task-submissions', $submissionDetails['file']);

        if (!$filePath) {
            throw new \RuntimeException('Failed to upload file to S3');
        }

        $data = [
            'user_id'    => $user->id,
            'council_id' => $user->council_id,
            'task_id'    => $submissionDetails['task_id'],
            'file'       => $filePath,
            'status'     => TaskStatusEnum::SUBMITTED->value,
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
