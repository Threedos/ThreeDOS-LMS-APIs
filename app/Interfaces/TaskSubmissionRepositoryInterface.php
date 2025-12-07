<?php

namespace App\Interfaces;

interface TaskSubmissionRepositoryInterface
{
    public function getAllTaskSubmissions();
    public function getTaskSubmissionById($submissionId);
    public function createTaskSubmission(array $submissionDetails);
    public function updateTaskSubmission($submissionId, array $newDetails);
    public function deleteTaskSubmission($submissionId);
}
