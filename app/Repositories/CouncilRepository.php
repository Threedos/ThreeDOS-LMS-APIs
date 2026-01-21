<?php

namespace App\Repositories;

use App\Http\Requests\CouncilRequests\AllCouncilRequest;
use App\Interfaces\CouncilRepositoryInterface;
use App\Models\Council;

class CouncilRepository implements CouncilRepositoryInterface
{
    public function getAllCouncils(AllCouncilRequest $request)
    {
        $query = Council::query()
        ->with([
            'Head' => function ($q) {
                $q->whereHas('role', function ($roleQuery) {
                    $roleQuery->where('name', 'Head');
                });
            }
        ]);

    if ($request->search) {
        $query->where('name', 'like', '%' . $request->search . '%')
        ->orderBy('created_at', 'desc');
    }

        return $query->get();
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
