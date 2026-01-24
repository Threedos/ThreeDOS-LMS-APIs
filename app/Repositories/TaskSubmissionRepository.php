<?php

namespace App\Repositories;

use App\Interfaces\TaskSubmissionRepositoryInterface;
use App\Models\TaskSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;


class TaskSubmissionRepository implements TaskSubmissionRepositoryInterface
{


    private function baseQuery($filters)
    {
        $search = $filters['search'] ?? null;
        $council_id = $filters['council_id'] ?? null;

        $query = TaskSubmission::query();

        if ($search) {
            $query->where('title', 'like', "%{$search}%"); // optional
        }

        if ($council_id) {
            $query->whereHas('task.council_session', function ($q) use ($council_id) {
                $q->where('council_id', $council_id);
            });
        }
        $query->with(['task.council_session.council', 'user']);

        return $query;
    }

    public function getAllTaskSubmissionsForUser(array $filters)
    {
        $pageIndex = $filters['pageIndex'] ?? 1;
        $pageSize = $filters['pageSize'] ?? 10;
        $query = $this->baseQuery($filters)->orderBy('created_at', 'desc');

        if (!empty($filters['task_id'])) {
            $query->where('task_id', $filters['task_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $paginator = $query->paginate($pageSize, ['*'], 'pageIndex', $pageIndex);
        return $paginator;
    }


    public function getAllTaskSubmissionsForCouncil(array $filters)
    {
        $pageIndex = $filters['pageIndex'] ?? 1;
        $pageSize = $filters['pageSize'] ?? 10;
        $query = $this->baseQuery($filters)->orderBy('created_at', 'desc');

        if (!empty($filters['task_id'])) {
            $query->where('task_id', $filters['task_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $paginator = $query->paginate($pageSize, ['*'], 'pageIndex', $pageIndex);
        return $paginator;
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
