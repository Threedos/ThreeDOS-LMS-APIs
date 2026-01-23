<?php

namespace App\Repositories;

use App\Interfaces\CouncilSessionRepositoryInterface;
use App\Models\CouncilSession;

class CouncilSessionRepository implements CouncilSessionRepositoryInterface
{
    public function getAllSessions(array $filters)
    {
        $council_id = $filters['council_id'];

        if ($council_id === null && $filters['role'] === 'VicePresident') {
            return CouncilSession::withCount('attendance')
                ->when($filters['search'], fn($q) => $q->where('title', 'like', "%{$filters['search']}%"))
                ->orderBy('created_at', 'desc')
                ->paginate($filters['pageSize'], ['*'], 'pageIndex', $filters['pageIndex']);
        }
        return CouncilSession::where('council_id', $council_id)
            ->withCount('attendance')
            ->when($filters['search'], fn($q) => $q->where('title', 'like', "%{$filters['search']}%"))
            ->orderBy('created_at', 'desc')
            ->paginate($filters['pageSize'], ['*'], 'pageIndex', $filters['pageIndex']);
    }

    public function getSessionById($id)
    {
        return CouncilSession::withCount('attendance')->findOrFail($id);
    }

    public function createSession(array $details)
    {
        return CouncilSession::create($details);
    }

    public function updateSession($id, array $details)
    {
        $session = CouncilSession::findOrFail($id);
        $session->update($details);
        return $session;
    }

    public function deleteSession($id)
    {
        $session = CouncilSession::findOrFail($id);
        return $session->delete();
    }
}
