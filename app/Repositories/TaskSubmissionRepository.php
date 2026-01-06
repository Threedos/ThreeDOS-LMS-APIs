<?php

namespace App\Repositories;

use App\Interfaces\TaskSubmissionRepositoryInterface;
use App\Models\TaskSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;


class TaskSubmissionRepository implements TaskSubmissionRepositoryInterface
{


    private function baseQuery($taskSubmissionPaginatedRequest){
     
        $search = $taskSubmissionPaginatedRequest->search ?? null;
        $filter = auth()->user()->council_id ?? null;
        $query = TaskSubmission::query();
        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }
        if ($filter) {
            $query->where('council_id', '=', $filter);
        }
        return $query;
    }
    public function getAllTaskSubmissionsForUser($taskSubmissionPaginatedRequest)
    {
           $pageIndex = $taskSubmissionPaginatedRequest->pageIndex ?? 1;
        $pageSize = $taskSubmissionPaginatedRequest->pageSize ?? 10;
        $query = $this->baseQuery($taskSubmissionPaginatedRequest);
        $query->where('user_id', '=', auth()->user()->id)->skip(($pageIndex - 1) * $pageSize)->take($pageSize);
        return $query->get();
    }


     public function getAllTaskSubmissionsForCouncil($taskSubmissionPaginatedRequest)
    {
        $pageIndex = $taskSubmissionPaginatedRequest->pageIndex ?? 1;
        $pageSize = $taskSubmissionPaginatedRequest->pageSize ?? 10;
        $query = $this->baseQuery($taskSubmissionPaginatedRequest);
        $query->skip(($pageIndex - 1) * $pageSize)->take($pageSize);
        return $query->get();
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
