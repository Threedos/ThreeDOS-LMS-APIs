<?php

namespace App\Repositories;

use App\Interfaces\TeamRepositoryInterface;
use App\Models\Team;

class TeamRepository implements TeamRepositoryInterface
{
    public function getAllTeams()
    {
        return Team::all()->orderBy('created_at', 'desc');
    }

    public function getTeamById($id)
    {
        return Team::findOrFail($id);
    }

    public function createTeam(array $details)
    {
        return Team::create($details);
    }

    public function updateTeam($id, array $details)
    {
        $team = Team::findOrFail($id);
        $team->update($details);
        return $team;
    }

    public function deleteTeam($id)
    {
        return Team::destroy($id);
    }
}
