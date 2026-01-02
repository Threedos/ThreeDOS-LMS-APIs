<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All roles (Head, Instructor, Delegate) can view tasks list if they are authenticated
        // Logic might need to be filtered by council in the controller/service, 
        // but policy-wise, they are allowed to 'viewAny'.
        return in_array($user->role->name, ['Instructor', 'Head', 'Delegate']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        // Users can view tasks belonging to their council
        return $user->council_id === $task->council_id;
    }


    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role->name === 'Instructor' || $user->role->name === 'Head';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        return ($user->role->name === 'Instructor' || $user->role->name === 'Head') && $user->council_id === $task->council_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return ($user->role->name === 'Instructor' || $user->role->name === 'Head') && $user->council_id === $task->council_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return ($user->role->name === 'Instructor' || $user->role->name === 'Head') && $user->council_id === $task->council_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return ($user->role->name === 'Instructor' || $user->role->name === 'Head') && $user->council_id === $task->council_id;
    }
}
