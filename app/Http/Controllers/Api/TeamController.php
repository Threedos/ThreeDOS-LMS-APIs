<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Resources\TeamResource;
use App\Services\TeamService;
use App\Services\CacheService;

class TeamController extends Controller
{
    use AuthorizesRequests;
    protected $teamService;
    protected $cacheService;

    public function __construct(TeamService $teamService, CacheService $cacheService)
    {
        $this->teamService = $teamService;
        $this->cacheService = $cacheService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Team::class);
        $teams = $this->teamService->getAllTeams($request->all());

        return $this->successResponse(
            TeamResource::collection($teams),
            'Teams retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeamRequest $request)
    {
        $this->authorize('create', Team::class);
        $team = $this->teamService->createTeam($request->validated());

        // Clear team cache
        $this->cacheService->clearResourceCache('teams');

        return $this->createdResponse(['id' => $team->id], 'Team created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $team = $this->teamService->getTeamById($id);
        $this->authorize('view', $team);

        return $this->successResponse(
            new TeamResource($team),
            'Team retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $team = Team::findOrFail($id);
        $this->authorize('update', $team);
        $this->teamService->updateTeam($id, $request->all());

        // Clear team cache
        $this->cacheService->forget("team:{$id}");
        $this->cacheService->clearResourceCache('teams');

        return $this->successResponse(null, 'Team updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $team = Team::findOrFail($id);
        $this->authorize('delete', $team);
        $this->teamService->deleteTeam($id);

        // Clear team cache
        $this->cacheService->forget("team:{$id}");
        $this->cacheService->clearResourceCache('teams');

        return $this->noContentResponse('Team deleted successfully');
    }
}
