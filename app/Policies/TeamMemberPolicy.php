<?php

namespace App\Policies;

use App\Models\TeamMember;
use App\Models\User;
use App\Enums\RolesEnum;
use Illuminate\Auth\Access\Response;

class TeamMemberPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, [RolesEnum::Head->value, RolesEnum::Instructor->value, RolesEnum::VicePresident->value, RolesEnum::President->value]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TeamMember $teamMember): bool
    {
        if (in_array($user->role->name, [RolesEnum::Head->value, RolesEnum::Instructor->value, RolesEnum::VicePresident->value, RolesEnum::President->value])) {
            return $user->council_id === $teamMember->team->council_id;
        }

        return $user->id === $teamMember->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role->name, [RolesEnum::Head->value, RolesEnum::Instructor->value]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TeamMember $teamMember): bool
    {
        return in_array($user->role->name, [RolesEnum::Head->value, RolesEnum::Instructor->value])
            && $user->council_id === $teamMember->team->council_id
            || $user->role->name === RolesEnum::VicePresident->value
            || $user->role->name === RolesEnum::President->value;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TeamMember $teamMember): bool
    {
        if (in_array($user->role->name, [RolesEnum::VicePresident->value, RolesEnum::President->value])) {
            return true;
        }
        return in_array($user->role->name, [RolesEnum::Head->value, RolesEnum::Instructor->value])
            && $user->council_id === $teamMember->team->council_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TeamMember $teamMember): bool
    {
        return in_array($user->role->name, [RolesEnum::Head->value, RolesEnum::Instructor->value])
            && $user->council_id === $teamMember->team->council_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TeamMember $teamMember): bool
    {
        return in_array($user->role->name, [RolesEnum::Head->value, RolesEnum::Instructor->value, RolesEnum::VicePresident->value, RolesEnum::President->value])
            && $user->council_id === $teamMember->team->council_id;
    }
}
