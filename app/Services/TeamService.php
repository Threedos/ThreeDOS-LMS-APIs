<?php

namespace App\Services;

use App\Interfaces\TeamRepositoryInterface;

class TeamService
{
    protected $teamRepository;

    public function __construct(TeamRepositoryInterface $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    public function getAllTeams()
    {
        return $this->teamRepository->getAllTeams();
    }

    public function getTeamById($id)
    {
        return $this->teamRepository->getTeamById($id);
    }

    public function createTeam(array $details)
    {
        return $this->teamRepository->createTeam($details);
    }

    public function updateTeam($id, array $details)
    {
        return $this->teamRepository->updateTeam($id, $details);
    }

    public function deleteTeam($id)
    {
        return $this->teamRepository->deleteTeam($id);
    }
}
