<?php

namespace App\Repositories;

use App\Interfaces\CouncilSessionRepositoryInterface;
use App\Models\CouncilSession;

class CouncilSessionRepository implements CouncilSessionRepositoryInterface
{
    public function getAllSessions($request)
    {
        $council_id = $request->user()->council_id;
        return CouncilSession::where('council_id', $council_id)
            ->withCount('attendance')
            ->when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->orderBy('created_at', 'desc')
            ->paginate($request->pageSize, ['*'], 'pageIndex', $request->pageIndex);
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
