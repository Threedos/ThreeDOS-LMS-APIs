<?php

namespace App\Services;

use App\Interfaces\TaskSubmissionRepositoryInterface;
use App\Http\Requests\TaskSubmissionRequests\TaskSubmissionPaginatedRequest;
use App\Notifications\EventNotification;
use App\Enums\TaskStatusEnum;
use App\Enums\RolesEnum;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class TaskSubmissionService
{
    protected $taskSubmissionRepository;

    public function __construct(TaskSubmissionRepositoryInterface $taskSubmissionRepository)
    {
        $this->taskSubmissionRepository = $taskSubmissionRepository;
    }

    public function getPaginatedSubmissions(TaskSubmissionPaginatedRequest $request)
    {
        $user = $request->user();
        $filters = $request->validated();

        if (in_array($user->role->name, [RolesEnum::VicePresident->value, RolesEnum::President->value])) {
            return $this->taskSubmissionRepository->getAllTaskSubmissionsForCouncil($filters);
        }

        // Add auth-context for others
        $filters['council_id'] = $user->council_id;

        if ($user->role->name === RolesEnum::Instructor->value || $user->role->name === RolesEnum::Head->value || $user->role->name === RolesEnum::HR->value) {
            return $this->taskSubmissionRepository->getAllTaskSubmissionsForCouncil($filters);
        } elseif ($user->role->name === RolesEnum::Delegate->value) {
            $filters['user_id'] = $user->id; // Force own ID for delegates
            return $this->taskSubmissionRepository->getAllTaskSubmissionsForUser($filters);
        }
        return collect();
    }


    public function getTaskSubmissionById($submissionId)
    {
        return $this->taskSubmissionRepository->getTaskSubmissionById($submissionId);
    }
    public function createTaskSubmission(array $submissionDetails)
    {
        $user = auth()->user();

        // 1. Resolve user_id: Use provided one or default to auth user
        $delegateId = $submissionDetails['user_id'] ?? $user->id;

        // 2. Force own ID for delegates (Prevents them from submitting for others)
        if ($user->role->name === RolesEnum::Delegate->value) {
            $delegateId = $user->id;
        }

        // 3. Security Check: Ensure Task and Target User belong to the same council (for non-admins)
        if (!in_array($user->role->name, [RolesEnum::VicePresident->value, RolesEnum::President->value])) {
            // Validate Task
            $task = Task::with('councilSession')->findOrFail($submissionDetails['task_id']);
            if ($task->councilSession->council_id !== $user->council_id) {
                throw new AuthorizationException('Unauthorized task submission. Task belongs to another council.');
            }

            // Validate Delegate (if submitting for someone else)
            if ($delegateId !== $user->id) {
                $targetUser = User::findOrFail($delegateId);
                if ($targetUser->council_id !== $user->council_id) {
                    throw new AuthorizationException('Target delegate does not belong to your council.');
                }
            }
        }

        $data = [
            'user_id' => $delegateId,
            'task_id' => $submissionDetails['task_id'],
            'file' => $submissionDetails['file'],
            'status' => TaskStatusEnum::SUBMITTED->value,
        ];

        return $this->taskSubmissionRepository->createTaskSubmission($data);
    }


    public function updateTaskSubmission($submissionId, array $submissionDetails)
    {
        $submission = $this->taskSubmissionRepository
            ->updateTaskSubmission($submissionId, $submissionDetails);

        return $submission;
    }

    public function deleteTaskSubmission($submissionId)
    {
        return $this->taskSubmissionRepository->deleteTaskSubmission($submissionId);
    }
}
