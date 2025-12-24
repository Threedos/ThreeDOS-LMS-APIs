<?php

namespace App\Interfaces;

use App\Http\Requests\CouncilRequests\AllCouncilRequest;
interface CouncilRepositoryInterface
{
    public function getAllCouncils(AllCouncilRequest $request);
    public function getCouncilById($councilId);
    public function createCouncil(array $councilDetails);
    public function updateCouncil($councilId, array $newDetails);
    public function deleteCouncil($councilId);
}
