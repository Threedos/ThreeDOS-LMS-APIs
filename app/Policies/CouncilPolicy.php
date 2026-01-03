<?php

namespace App\Policies;

use App\Models\Council;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CouncilPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Council $council): bool
    {
        // Users can view their own council
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only Head or Instructor can create? Actually, maybe only Admin?
        // Assuming the prompt implies Head/Instructor manage things.
        // Usually Council creation might be Super Admin or Head.
        // Let's stick to user request: "Head, Instructor, Delegate(which only view most of models)"
        return $user->role->name === 'Instructor' || $user->role->name === 'Head';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Council $council): bool
    {
        return ($user->role->name === 'Instructor' || $user->role->name === 'Head') && $user->council_id === $council->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Council $council): bool
    {
        return ($user->role->name === 'Instructor' || $user->role->name === 'Head') && $user->council_id === $council->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Council $council): bool
    {
        return ($user->role->name === 'Instructor' || $user->role->name === 'Head') && $user->council_id === $council->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Council $council): bool
    {
        return ($user->role->name === 'Instructor' || $user->role->name === 'Head') && $user->council_id === $council->id;
    }
}
