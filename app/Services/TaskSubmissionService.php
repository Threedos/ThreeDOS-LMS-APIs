<?php

namespace App\Services;

use App\Interfaces\TaskSubmissionRepositoryInterface;

class TaskSubmissionService
{
    protected $taskSubmissionRepository;

    public function __construct(TaskSubmissionRepositoryInterface $taskSubmissionRepository)
    {
        $this->taskSubmissionRepository = $taskSubmissionRepository;
    }

    public function getAllTaskSubmissions()
    {
        return $this->taskSubmissionRepository->getAllTaskSubmissions();
    }

    public function getTaskSubmissionById($submissionId)
    {
        return $this->taskSubmissionRepository->getTaskSubmissionById($submissionId);
    }

    public function createTaskSubmission(array $submissionDetails)
    {
        return $this->taskSubmissionRepository->createTaskSubmission($submissionDetails);
    }

    public function updateTaskSubmission($submissionId, array $submissionDetails)
    {
        return $this->taskSubmissionRepository->updateTaskSubmission($submissionId, $submissionDetails);
    }

    public function deleteTaskSubmission($submissionId)
    {
        return $this->taskSubmissionRepository->deleteTaskSubmission($submissionId);
    }
}
