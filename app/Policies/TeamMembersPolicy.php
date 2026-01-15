<?php

namespace App\Policies;

use App\Models\TeamMembers;
use App\Models\User;
use App\Enums\RolesEnum;
use Illuminate\Auth\Access\Response;

class TeamMembersPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, [RolesEnum::Head->value, RolesEnum::Instructor->value]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TeamMembers $teamMembers): bool
    {
        if (in_array($user->role->name, [RolesEnum::Head->value, RolesEnum::Instructor->value])) {
            return $user->council_id === $teamMembers->team->council_id;
        }

        return $user->id === $teamMembers->user_id;
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
    public function update(User $user, TeamMembers $teamMembers): bool
    {
        return in_array($user->role->name, [RolesEnum::Head->value, RolesEnum::Instructor->value])
            && $user->council_id === $teamMembers->team->council_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TeamMembers $teamMembers): bool
    {
        return in_array($user->role->name, [RolesEnum::Head->value, RolesEnum::Instructor->value])
            && $user->council_id === $teamMembers->team->council_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TeamMembers $teamMembers): bool
    {
        return in_array($user->role->name, [RolesEnum::Head->value, RolesEnum::Instructor->value])
            && $user->council_id === $teamMembers->team->council_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TeamMembers $teamMembers): bool
    {
        return in_array($user->role->name, [RolesEnum::Head->value, RolesEnum::Instructor->value])
            && $user->council_id === $teamMembers->team->council_id;
    }
}
