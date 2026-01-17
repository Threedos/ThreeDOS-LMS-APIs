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
        return response()->json($teams);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeamRequest $request)
    {
        //
        $team = Team::create($request->all());
        
        return response()->json( $team->id, 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $team = Team::findOrFail($id);
        $team = new TeamResource($team);
        return response()->json($team);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        //
        $team->update($request->all());
        return response()->json("Updated Successfully", 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        Team::destroy($id);
        return response()->json('Deleted Successfully', 204);
    }
}
