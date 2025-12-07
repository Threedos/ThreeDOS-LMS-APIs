<?php

namespace App\Interfaces;

interface CouncilRepositoryInterface
{
    public function getAllCouncils();
    public function getCouncilById($councilId);
    public function createCouncil(array $councilDetails);
    public function updateCouncil($councilId, array $newDetails);
    public function deleteCouncil($councilId);
}
