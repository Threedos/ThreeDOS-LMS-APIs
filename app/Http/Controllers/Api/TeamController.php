<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Resources\TeamResource;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $teams = Team::all();
        $teams = TeamResource::collection($teams);
        return $this->successResponse($teams, 'Teams retrieved successfully');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeamRequest $request)
    {
        //
        $team = Team::create($request->all());

        return $this->createdResponse(['id' => $team->id], 'Team created successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $team = Team::findOrFail($id);
        $team = new TeamResource($team);
        return $this->successResponse($team, 'Team retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        //
        $team->update($request->all());
        return $this->successResponse(null, 'Team updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        Team::destroy($id);
        return $this->noContentResponse('Team deleted successfully');
    }
}
