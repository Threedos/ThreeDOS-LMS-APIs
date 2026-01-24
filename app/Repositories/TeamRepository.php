<?php

namespace App\Repositories;

use App\Interfaces\TeamRepositoryInterface;
use App\Models\Team;

class TeamRepository implements TeamRepositoryInterface
{
    public function getAllTeams(array $filters)
    {
        $council_id = $filters['council_id'] ?? null;
        $is_vice_president = $filters['is_vice_president'] ?? false;

        if ($council_id === null && $is_vice_president) {
            return Team::orderBy('created_at', 'desc')->get();
        }

        return Team::where('council_id', $council_id)->orderBy('created_at', 'desc')->get();
    }

    public function getTeamById($id)
    {
        return Team::with('teamMembers.user:id,name,email')->findOrFail($id);
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
