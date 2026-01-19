<?php

namespace App\Repositories;

use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;

class TaskRepository implements TaskRepositoryInterface
{
    public function getAllTasks($request)
    {
        $pageIndex = $request->pageIndex ?? 1;
        $pageSize = $request->pageSize ?? 10;
        $search = $request->search;
        $filter = $request->filter;
        $query = Task::query();
        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }
        if ($filter) {
            $query->where('council_id', '=', $filter);
        }
       // $query->skip(($pageIndex - 1) * $pageSize)->take($pageSize);
        

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
