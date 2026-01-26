<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Enums\RolesEnum;

class UserPolicy
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
    public function view(User $user, User $model): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if (in_array($user->role->name, [
            RolesEnum::Head->value,
            RolesEnum::Instructor->value,
            RolesEnum::VicePresident->value,
            RolesEnum::President->value
        ], true)) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $authUser, User $targetUser): bool
    {
        if (in_array($authUser->role->name, [
            RolesEnum::Head->value,
            RolesEnum::Instructor->value,
            RolesEnum::VicePresident->value,
            RolesEnum::President->value
        ], true)) {
            return true;
        }

        return
            $authUser->role->name === RolesEnum::Delegate->value &&
            $authUser->id === $targetUser->id;
    }


    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        if (in_array($user->role->name, [
            RolesEnum::Head->value,
            RolesEnum::Instructor->value,
            RolesEnum::VicePresident->value,
            RolesEnum::President->value
        ], true)) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        if (in_array($user->role->name, [
            RolesEnum::Head->value,
            RolesEnum::Instructor->value,
            RolesEnum::VicePresident->value
        ], true)) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        if (in_array($user->role->name, [
            RolesEnum::Head->value,
            RolesEnum::Instructor->value,
            RolesEnum::VicePresident->value
        ], true)) {
            return true;
        }
        return false;
    }
}
