<?php

namespace App\Services;

use App\Interfaces\CouncilRepositoryInterface;
use App\Http\Requests\CouncilRequests\AllCouncilRequest;
class CouncilService
{
    protected $councilRepository;

    public function __construct(CouncilRepositoryInterface $councilRepository)
    {
        $this->councilRepository = $councilRepository;
    }

    public function getAllCouncils(AllCouncilRequest $request)
    {
        return $this->councilRepository->getAllCouncils($request);
    }

    public function getCouncilById($councilId)
    {
        return $this->councilRepository->getCouncilById($councilId);
    }

    public function createCouncil(array $councilDetails)
    {
        return $this->councilRepository->createCouncil($councilDetails);
    }

    public function updateCouncil($councilId, array $councilDetails)
    {
        return $this->councilRepository->updateCouncil($councilId, $councilDetails);
    }

    public function deleteCouncil($councilId)
    {
        return $this->councilRepository->deleteCouncil($councilId);
    }
}
