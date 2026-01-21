<?php

namespace App\Repositories;

use App\Interfaces\TeamMemberRepositoryInterface;
use App\Models\TeamMember;

class TeamMemberRepository implements TeamMemberRepositoryInterface
{
    public function getAllTeamMembers()
    {
        return TeamMember::all();
    }

    public function getTeamMemberById($id)
    {
        return TeamMember::findOrFail($id);
    }

    public function bulkCreateTeamMembers(array $members)
    {
        foreach ($members as &$member) {
            $member['created_at'] = now();
            $member['updated_at'] = now();
        }
        return TeamMember::insert($members);
    }

    public function updateTeamMember($id, array $details)
    {
        $member = TeamMember::findOrFail($id);
        $member->update($details);
        return $member;
    }

    public function deleteTeamMember($id)
    {
        return TeamMember::destroy($id);
    }
}
