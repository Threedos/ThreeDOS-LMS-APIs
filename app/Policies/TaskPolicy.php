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
    // Instructor or Head can view all tasks
    if ($user->role === 'Instructor' || $user->role === 'Head') {
        return true;
    }

    // Students can “view” their own tasks
    return $user->role === 'Student';    

}

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
   // Instructor or Head can view any task
    if ($user->role === 'Instructor' || $user->role === 'Head') {
        return true;
    }

    // Students can only view their own tasks
    if ($user->role === 'Student') {
        return $task->student_id === $user->id;
    }

    // Default deny
    return false;    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'Instructor' || $user->role === 'Head';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        return $user->role === 'Instructor' || $user->role === 'Head';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->role === 'Instructor' || $user->role === 'Head';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return $user->role === 'Instructor' || $user->role === 'Head';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return $user->role === 'Instructor' || $user->role === 'Head';
    }
}
