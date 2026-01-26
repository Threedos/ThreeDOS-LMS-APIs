<?php

namespace App\Policies;

use App\Models\Council;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Enums\RolesEnum;
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
        return $user->role->name === RolesEnum::Instructor->value 
        || $user->role->name === RolesEnum::Head->value 
        || $user->role->name===RolesEnum::VicePresident->value
        || $user->role->name===RolesEnum::President->value;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Council $council): bool
    {
        return (($user->role->name === RolesEnum::Instructor->value || $user->role->name === RolesEnum::Head->value) 
        && $user->council_id === $council->id) ||  $user->role->name===RolesEnum::VicePresident->value
        || $user->role->name===RolesEnum::President->value;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Council $council): bool
    {
        return (($user->role->name === RolesEnum::Instructor->value || $user->role->name === RolesEnum::Head->value) && $user->council_id === $council->id) 
        ||  $user->role->name===RolesEnum::VicePresident->value
        || $user->role->name===RolesEnum::President->value;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Council $council): bool
    {
        return (($user->role->name === RolesEnum::Instructor->value || $user->role->name === RolesEnum::Head->value) && $user->council_id === $council->id) 
        ||  $user->role->name===RolesEnum::VicePresident->value
        || $user->role->name===RolesEnum::President->value;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Council $council): bool
    {
        return (($user->role->name === RolesEnum::Instructor->value || $user->role->name === RolesEnum::Head->value) && $user->council_id === $council->id)
        ||  $user->role->name===RolesEnum::VicePresident->value
        || $user->role->name===RolesEnum::President->value;
    }
}
