<?php
namespace App\Policies;

use App\Models\User;
use App\Models\TaskSubmission;

class TaskSubmissionPolicy
{
    /**
     * Council-wide listing (index)
     */
    public function viewAny(User $user): bool
    {
        return $user->role->name === "Head" || $user->role->name === "Instructor";
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
                ($user->role->name === "Head" || $user->role->name === "Instructor") &&
                $submission->council_id === $user->council_id
            );
    }

    /**
     * Create submission
     */
    public function create(User $user): bool
    {
        // Delegate can submit task (create submission)
        return ($user->role->name === 'Delegate' || $user->role->name === 'Instructor' || $user->role->name === 'Head') && $user->council_id !== null ;
    }

    /**
     * Update submission
     */
    public function update(User $user, TaskSubmission $submission): bool
    {
        return $submission->user_id === $user->id || (($user->role->name === "Head" || $user->role->name === "Instructor") && $submission->council_id === $user->council_id);
    }

    /**
     * Delete submission
     */
    public function delete(User $user, TaskSubmission $submission): bool
    {
        return
            $submission->user_id === $user->id ||
            (
                ($user->role->name === "Head" || $user->role->name === "Instructor") &&
                $submission->council_id === $user->council_id
            );
    }
}
