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

    public function getAllTeams(array $filters = [])
    {
        $user = auth()->user();

        // Base filters from user context
        if (!in_array($user->role->name, ['VicePresident', 'President'])) {
            $filters['council_id'] = $user->council_id;
        }

        $filters['is_vice_president'] = in_array($user->role->name, ['VicePresident', 'President']);

        // If a Delegate is requesting, they can only see their own teams unless filtered otherwise (which they shouldn't be able to)
        if ($user->role->name === 'Delegate') {
            $filters['user_id'] = $user->id;
        }

        return $this->teamRepository->getAllTeams($filters);
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
