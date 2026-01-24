<?php

namespace App\Interfaces;

interface TeamMemberRepositoryInterface
{
    public function getAllTeamMembers(array $filters);
    public function getTeamMemberById($id);
    public function createTeamMember(array $details);
    public function bulkCreateTeamMembers(array $members);
    public function updateTeamMember($id, array $details);
    public function deleteTeamMember($id);
}
