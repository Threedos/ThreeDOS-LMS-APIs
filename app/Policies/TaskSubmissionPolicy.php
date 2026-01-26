<?php
namespace App\Policies;

use App\Models\User;
use App\Models\TaskSubmission;
use App\Enums\RolesEnum;

class TaskSubmissionPolicy
{
    /**
     * Council-wide listing (index)
     */
    public function viewAny(User $user): bool
    {
        return $user->role->name === RolesEnum::Head->value || $user->role->name === RolesEnum::Instructor->value || $user->role->name === RolesEnum::VicePresident->value;
    }


    
    // User-scoped listing
    public function viewOwn(User $user): bool
    {
        return true; // any authenticated user can view their own submissions
    }
    /**
     * View single submission
     */
    public function view(User $user, TaskSubmission $submission): bool
    {
        return
            $submission->user_id === $user->id ||
            (
                ($user->role->name === RolesEnum::Head->value || $user->role->name === RolesEnum::Instructor->value) &&
                $submission->task->council_session->council_id === $user->council_id
            )
            || $user->role->name === RolesEnum::VicePresident->value
            || $user->role->name === RolesEnum::President->value;
    }

    /**
     * Create submission
     */
    public function create(User $user): bool
    {
        // Delegate can submit task (create submission)
        return (($user->role->name === RolesEnum::Delegate->value || $user->role->name === RolesEnum::Instructor->value || $user->role->name === RolesEnum::Head->value)
        && $user->council_id !== null )
        || $user->role->name === RolesEnum::VicePresident->value
        || $user->role->name === RolesEnum::President->value;
    }

    /**
     * Update submission
     */
    public function update(User $user, TaskSubmission $submission): bool
    {
        return $submission->user_id === $user->id || (($user->role->name === RolesEnum::Head->value || $user->role->name === RolesEnum::Instructor->value)
        && $submission->task->council_session->council_id === $user->council_id)
        || $user->role->name === RolesEnum::VicePresident->value
        || $user->role->name === RolesEnum::President->value;
    }

    /**
     * Delete submission
     */
    public function delete(User $user, TaskSubmission $submission): bool
    {
        return
            $submission->user_id === $user->id ||
            (
                ($user->role->name === RolesEnum::Head->value || $user->role->name === RolesEnum::Instructor->value) &&
                $submission->task->council_session->council_id === $user->council_id
            )
            || $user->role->name === RolesEnum::VicePresident->value
            || $user->role->name === RolesEnum::President->value;
    }
}
