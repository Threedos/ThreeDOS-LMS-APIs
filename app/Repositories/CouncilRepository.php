<?php

namespace App\Repositories;

use App\Interfaces\CouncilRepositoryInterface;
use App\Models\Council;

class CouncilRepository implements CouncilRepositoryInterface
{
    public function getAllCouncils()
    {
        return Council::all();
    }

    public function getCouncilById($councilId)
    {
        return Council::findOrFail($councilId);
    }

    public function createCouncil(array $councilDetails)
    {
        return Council::create($councilDetails);
    }

    public function updateCouncil($councilId, array $newDetails)
    {
        return Council::whereId($councilId)->update($newDetails);
    }

    public function deleteCouncil($councilId)
    {
        Council::destroy($councilId);
    }
}
