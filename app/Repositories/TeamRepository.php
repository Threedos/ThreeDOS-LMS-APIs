<?php

namespace App\Repositories;

use App\Interfaces\TeamRepositoryInterface;
use App\Models\Team;
use App\Models\TeamMember;
 use Illuminate\Support\Facades\DB;
class TeamRepository implements TeamRepositoryInterface
{
    public function getAllTeams(array $filters)
    {
        $council_id = $filters['council_id'] ?? null;
        $is_vice_president = $filters['is_vice_president'] ?? false;
        $user_id = $filters['user_id'] ?? null;

        $query = Team::query();

        if ($council_id !== null) {
            $query->where('council_id', $council_id);
        } elseif (!$is_vice_president) {
            // If not VP and no council_id provided (shouldn't happen with Service logic), 
            // we might want to return nothing or handle it. For now,Service ensures council_id or VP.
        }

        if ($user_id) {
            $query->whereHas('teamMembers', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
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
            return DB::transaction(function () use ($id) {
                TeamMember::where('team_id', $id)->delete();
                return Team::destroy($id);
            });
        }

    
}
