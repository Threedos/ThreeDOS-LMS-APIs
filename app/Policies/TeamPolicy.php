<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use App\Enums\RolesEnum;
use Illuminate\Auth\Access\Response;

class TeamPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, [
            RolesEnum::Head->value,
            RolesEnum::Instructor->value,
            RolesEnum::HR->value,
            RolesEnum::Delegate->value,
            RolesEnum::VicePresident->value,
            RolesEnum::President->value
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Team $team): bool
    {
        if (in_array($user->role->name, [RolesEnum::Head->value,
            RolesEnum::Instructor->value,
            RolesEnum::VicePresident->value,
            RolesEnum::President->value,
            RolesEnum::HR->value])) {
            return $user->council_id === $team->council_id;
        }

        if ($user->role->name === RolesEnum::Delegate->value) {
            return $team->teamMembers()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role->name, [RolesEnum::Head->value, RolesEnum::Instructor->value, RolesEnum::VicePresident->value, RolesEnum::President->value]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Team $team): bool
    {
        if (in_array($user->role->name, [RolesEnum::Head->value, RolesEnum::Instructor->value])) {
            return $user->council_id === $team->council_id;
        }

        if ($user->role->name === RolesEnum::Delegate->value) {
            return $team->teamMembers()
                ->where('user_id', $user->id)
                ->whereIn('role', ['Leader', 'Co-Leader'])
                ->exists();
        }

        if ($user->role->name === RolesEnum::VicePresident->value) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Team $team): bool
    {
        if (in_array($user->role->name, [RolesEnum::VicePresident->value, RolesEnum::President->value])) {
            return true;
        }
        return in_array($user->role->name, [RolesEnum::Head->value, RolesEnum::Instructor->value])
            && $user->council_id === $team->council_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Team $team): bool
    {
        if (in_array($user->role->name, [RolesEnum::VicePresident->value, RolesEnum::President->value])) {
            return true;
        }
        return in_array($user->role->name, [RolesEnum::Head->value, RolesEnum::Instructor->value])
            && $user->council_id === $team->council_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Team $team): bool
    {
        return in_array($user->role->name, [RolesEnum::Head->value, RolesEnum::Instructor->value])
            && $user->council_id === $team->council_id;
    }
}
