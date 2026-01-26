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
        $user = $request->user();
        $filters = [
            'search' => $request->search,
        ];

        if (in_array($user->role->name, ['Delegate', 'Instructor', 'Head'])) {
            $filters['id'] = $user->council_id;
        } elseif ($user->role->name == 'VicePresident' || $user->role->name == 'President') {
            $filters['id'] = null;
        }

        return $this->councilRepository->getAllCouncils($filters);
    }

    public function getCouncilById($councilId)
    {
        return $this->councilRepository->getCouncilById($councilId);
    }

    public function createCouncil(array $councilDetails)
    {
        $user = auth()->user();
        if($user->role->name == 'VicePresident' || $user->role->name == 'Head'){
            return $this->councilRepository->createCouncil($councilDetails);
        }
        return response()->json([
            'message' => 'You are not authorized to create this council',
        ], 403);
    }

    public function updateCouncil($councilId, array $councilDetails)
    {
        $user = auth()->user();
        if($user->role->name == 'VicePresident' || ($user->role->name == 'Head' && $user->council_id == $councilId)){
            return $this->councilRepository->updateCouncil($councilId, $councilDetails);
        }
        return response()->json([
            'message' => 'You are not authorized to update this council',
        ], 403);
    }

    public function deleteCouncil($councilId)
    {
        $user = auth()->user();
        if($user->role->name == 'VicePresident' || ($user->role->name == 'Head' && $user->council_id == $councilId)){
            return $this->councilRepository->deleteCouncil($councilId);
        }
        return response()->json([
            'message' => 'You are not authorized to delete this council',
        ], 403);
    }
}
