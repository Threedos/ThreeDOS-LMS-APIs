<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Resources\TeamResource;
use App\Services\TeamService;

class TeamController extends Controller
{
    protected $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teams = $this->teamService->getAllTeams();
        return $this->successResponse(TeamResource::collection($teams), 'Teams retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeamRequest $request)
    {
        $team = $this->teamService->createTeam($request->validated());
        return $this->createdResponse(['id' => $team->id], 'Team created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $team = $this->teamService->getTeamById($id);
        return $this->successResponse(new TeamResource($team), 'Team retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->teamService->updateTeam($id, $request->all());
        return $this->successResponse(null, 'Team updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->teamService->deleteTeam($id);
        return $this->noContentResponse('Team deleted successfully');
    }
}
