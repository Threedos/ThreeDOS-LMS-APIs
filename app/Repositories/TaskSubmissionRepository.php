<?php

namespace App\Repositories;

use App\Interfaces\TaskSubmissionRepositoryInterface;
use App\Models\TaskSubmission;

class TaskSubmissionRepository implements TaskSubmissionRepositoryInterface
{
    public function getAllTaskSubmissions()
    {
        return TaskSubmission::all();
    }

    public function getTaskSubmissionById($submissionId)
    {
        return TaskSubmission::findOrFail($submissionId);
    }

    public function createTaskSubmission(array $submissionDetails)
    {
        return TaskSubmission::create($submissionDetails);
    }

    public function updateTaskSubmission($submissionId, array $newDetails)
    {
        return TaskSubmission::whereId($submissionId)->update($newDetails);
    }

    public function deleteTaskSubmission($submissionId)
    {
        TaskSubmission::destroy($submissionId);
    }
}
