<?php

namespace App\Services;

use App\Interfaces\TeamMemberRepositoryInterface;

class TeamMemberService
{
    protected $teamMemberRepository;

    public function __construct(TeamMemberRepositoryInterface $teamMemberRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
    }

    public function getAllTeamMembers()
    {
        $filters = []; // Add filters if needed
        return $this->teamMemberRepository->getAllTeamMembers($filters);
    }

    public function getTeamMemberById($id)
    {
        return $this->teamMemberRepository->getTeamMemberById($id);
    }
    public function createTeamMember(array $details)
    {
        return $this->teamMemberRepository->createTeamMember($details);
    }
    public function bulkCreateTeamMembers(array $members)
    {
        return $this->teamMemberRepository->bulkCreateTeamMembers($members);
    }

    public function updateTeamMember($id, array $details)
    {
        return $this->teamMemberRepository->updateTeamMember($id, $details);
    }

    public function deleteTeamMember($id)
    {
        return $this->teamMemberRepository->deleteTeamMember($id);
    }
}
