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
        return $this->sessionRepository->createSession($details);
    }

    public function updateSession($id, array $details)
    {
        return $this->sessionRepository->updateSession($id, $details);
    }

    public function deleteSession($id)
    {
        return $this->sessionRepository->deleteSession($id);
    }
}
