<?php

namespace App\Services;

use App\Interfaces\CouncilRepositoryInterface;

class CouncilService
{
    protected $councilRepository;

    public function __construct(CouncilRepositoryInterface $councilRepository)
    {
        $this->councilRepository = $councilRepository;
    }

    public function getAllCouncils()
    {
        return $this->councilRepository->getAllCouncils();
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
