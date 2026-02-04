<?php

namespace App\Repositories;

use App\Interfaces\TaskSubmissionRepositoryInterface;
use App\Models\TaskSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Task;


class TaskSubmissionRepository implements TaskSubmissionRepositoryInterface
{


    private function baseQuery($filters)
    {
        $search = $filters['search'] ?? null;
        $council_id = $filters['council_id'] ?? null;

        $query = TaskSubmission::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('task', function ($q2) use ($search) {
                    $q2->where('title', 'like', "%{$search}%");
                })->orWhereHas('user', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($council_id) {
            $query->whereHas('task.councilSession', function ($q) use ($council_id) {
                $q->where('council_id', $council_id);
            });
        }
        $query->with(['task.councilSession.council', 'user']);

        return $query;
    }

    public function getAllTaskSubmissionsForUser(array $filters)
    {
        $pageIndex = $filters['pageIndex'] ?? 1;
        $pageSize = $filters['pageSize'] ?? 10;
        if (!empty($filters['task_id']) && !empty($filters['user_id'])) {
            return $this->getAllTaskSubmissionsForCouncil($filters);
        }

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

        if (!empty($filters['task_id'])) {
            $task_id = $filters['task_id'];
            $task = Task::with('councilSession.council')->findOrFail($task_id);
            $council_id = $task->councilSession->council_id;

            $query = User::query()
                ->where('users.council_id', $council_id)
                ->whereHas('role', function ($q) {
                    $q->where('name', 'Delegate');
                })
                ->leftJoin('task_submissions', function ($join) use ($task_id) {
                    $join->on('users.id', '=', 'task_submissions.user_id')
                        ->where('task_submissions.task_id', '=', $task_id);
                })
                ->select([
                    'users.id as user_id',
                    'users.name as user_name',
                    'task_submissions.id as submission_id',
                    'task_submissions.file',
                    'task_submissions.grade',
                    'task_submissions.comment',
                    'task_submissions.status',
                    'task_submissions.created_at',
                    'task_submissions.updated_at',
                ]);

            if (!empty($filters['status'])) {
                if ($filters['status'] === 'not submitted yet') {
                    $query->whereNull('task_submissions.status');
                } else {
                    $query->where('task_submissions.status', $filters['status']);
                }
            }

            if (!empty($filters['user_id'])) {
                $query->where('users.id', $filters['user_id']);
            }

            if (!empty($filters['search'])) {
                $query->where('users.name', 'like', "%{$filters['search']}%");
            }

            $paginator = $query->paginate($pageSize, ['*'], 'pageIndex', $pageIndex);

            // Transform into TaskSubmission instances so Resource works
            $paginator->getCollection()->transform(function ($item) use ($task) {
                $submission = new TaskSubmission();
                $submission->id = $item->submission_id ?? \Illuminate\Support\Str::uuid();
                $submission->user_id = $item->user_id;
                $submission->task_id = $task->id;
                $submission->file = $item->file;
                $submission->grade = $item->grade;
                $submission->comment = $item->comment;
                $submission->status = $item->status ?? 'not submitted yet';
                $submission->created_at = $item->created_at;
                $submission->updated_at = $item->updated_at;

                // Load relations
                $user = new User();
                $user->id = $item->user_id;
                $user->name = $item->user_name;
                $submission->setRelation('user', $user);
                $submission->setRelation('task', $task);

                $submission->exists = $item->submission_id ? true : false;

                return $submission;
            });

            return $paginator;
        }

        $query = $this->baseQuery($filters)->orderBy('created_at', 'desc');

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
