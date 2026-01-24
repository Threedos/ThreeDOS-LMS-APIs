<?php

namespace App\Services;

use App\Interfaces\CouncilSessionRepositoryInterface;

class CouncilSessionService
{
    protected $sessionRepository;

    public function __construct(CouncilSessionRepositoryInterface $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public function getAllSessions(array $filters)
    {
        return $this->sessionRepository->getAllSessions($filters);
    }

    public function getSessionById($id)
    {
        return $this->sessionRepository->getSessionById($id);
    }

    public function createSession(array $details)
    {
        $user = auth()->user();
        if($user->role->name == 'VicePresident' || ($user->role->name == 'Head' && $user->council_id == $details['council_id'])){
            return $this->sessionRepository->createSession($details);
        }
        return response()->json([
            'message' => 'You are not authorized to create this session',
        ], 403);
    }

    public function updateSession($id, array $details)
    {
        $user = auth()->user();
        if($user->role->name == 'VicePresident' || ($user->role->name == 'Head' && $user->council_id == $details['council_id'])){
            return $this->sessionRepository->updateSession($id, $details);
        }
        return response()->json([
            'message' => 'You are not authorized to update this session',
        ], 403);
    }

    public function deleteSession($id)
    {
        $user = auth()->user();
        if($user->role->name == 'VicePresident' || (($user->role->name == 'Head'|| $user->role->name == 'Instructor') && $user->council_id == $id)){
            return $this->sessionRepository->deleteSession($id);
        }
        return response()->json([
            'message' => 'You are not authorized to delete this session',
        ], 403);
    }
}
