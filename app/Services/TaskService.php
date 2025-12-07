<?php

namespace App\Services;

use App\Interfaces\TaskRepositoryInterface;

class TaskService
{
    protected $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function getAllTasks()
    {
        return $this->taskRepository->getAllTasks();
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
