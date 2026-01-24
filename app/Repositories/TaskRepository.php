<?php

namespace App\Repositories;

use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;

class TaskRepository implements TaskRepositoryInterface
{
    public function getAllTasks(array $filters)
    {
        $pageIndex = $filters['pageIndex'] ?? 1;
        $pageSize = $filters['pageSize'] ?? 10;
        $search = $filters['search'] ?? null;
        $filter = $filters['filter'] ?? null;
        $query = Task::query();

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }
        if ($filter) {
            $query->where('council_id', '=', $filter);
        }
        $query->orderBy('created_at', 'desc');
        return $query->paginate($pageSize, ['*'], 'page', $pageIndex);
    }

    public function getTaskById($taskId)
    {
        return Task::findOrFail($taskId);
    }

    public function createTask(array $taskDetails)
    {
        return Task::create($taskDetails);
    }

    public function updateTask($taskId, array $newDetails)
    {
        return Task::whereId($taskId)->update($newDetails);
    }

    public function deleteTask($taskId)
    {
        Task::destroy($taskId);
    }
}
