<?php

namespace App\Interfaces;

interface CouncilSessionRepositoryInterface
{
    public function getAllSessions(array $filters);
    public function getSessionById($id);
    public function createSession(array $details);
    public function updateSession($id, array $details);
    public function deleteSession($id);
}
