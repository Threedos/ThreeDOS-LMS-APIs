<?php

namespace App\Repositories;

use App\Interfaces\TeamMemberRepositoryInterface;
use App\Models\TeamMember;

class TeamMemberRepository implements TeamMemberRepositoryInterface
{
    public function getAllTeamMembers(array $filters)
    {
        return TeamMember::orderBy('created_at', 'desc')->get(); // Filters can be applied here
    }

    public function getTeamMemberById($id)
    {
        return TeamMember::findOrFail($id);
    }
    public function createTeamMember(array $details)
    {
        return TeamMember::create($details);
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
