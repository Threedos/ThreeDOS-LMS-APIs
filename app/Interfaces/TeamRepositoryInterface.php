<?php

namespace App\Interfaces;

interface TeamRepositoryInterface
{
    public function getAllTeams(array $filters);
    public function getTeamById($id);
    public function createTeam(array $details);
    public function updateTeam($id, array $details);
    public function deleteTeam($id);
}
