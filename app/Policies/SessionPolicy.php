<?php

namespace App\Policies;

use App\Models\CouncilSession;
use App\Models\Session;
use App\Models\User;
use App\Enums\RolesEnum;
use Illuminate\Auth\Access\Response;

class SessionPolicy
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
    public function view(User $user, CouncilSession $session): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if($user->role->name == RolesEnum::Head->value || $user->role->name == RolesEnum::Instructor->value){
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CouncilSession $session): bool
    {
        if($user->role->name == RolesEnum::Head->value || $user->role->name == RolesEnum::Instructor->value){
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CouncilSession $session): bool
    {
        if($user->role->name == RolesEnum::Head->value || $user->role->name == RolesEnum::Instructor->value){
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CouncilSession $session): bool
    {
        if($user->role->name == RolesEnum::Head->value || $user->role->name == RolesEnum::Instructor->value){
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CouncilSession $session): bool
    {
        if($user->role->name == RolesEnum::Head->value || $user->role->name == RolesEnum::Instructor->value){
            return true;
        }
        return false;
    }
}
