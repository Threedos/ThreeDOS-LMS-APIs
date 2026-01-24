<?php

namespace App\Interfaces;

use App\Http\Requests\CouncilRequests\AllCouncilRequest;
interface CouncilRepositoryInterface
{
    public function getAllCouncils(array $filters);
    public function getCouncilById($councilId);
    public function createCouncil(array $councilDetails);
    public function updateCouncil($councilId, array $newDetails);
    public function deleteCouncil($councilId);
}
