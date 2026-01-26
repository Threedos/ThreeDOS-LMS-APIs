<?php

namespace App\Services;

use App\Interfaces\TaskRepositoryInterface;
use App\Http\Requests\TaskRequests\TaskPaginatedRequest;
class TaskService
{
    protected $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

public function getAllTasks(TaskPaginatedRequest $request)
{
    return $this->taskRepository->getAllTasks([
        'pageIndex' => $request->pageIndex,
        'pageSize'  => $request->pageSize,
        'search'    => $request->search,
        'filter'    => $request->filter, // optional
        'user'      => auth()->user(),
    ]);
}


    public function getTaskById($taskId)
    {
        return $this->taskRepository->getTaskById($taskId);
    }

    public function createTask(array $taskDetails)
    {
        return $this->taskRepository->createTask($taskDetails);
    }

    public function updateTask($taskId, array $taskDetails)
    {
        return $this->taskRepository->updateTask($taskId, $taskDetails);
    }

    public function deleteTask($taskId)
    {
        return $this->taskRepository->deleteTask($taskId);
    }
}
