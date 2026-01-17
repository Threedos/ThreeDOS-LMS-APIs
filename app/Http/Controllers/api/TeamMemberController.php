<?php

namespace App\Http\Controllers;

use App\Models\TeamMembers;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $teamMembers = TeamMembers::all();
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $teamMember = TeamMembers::create($request->all());
        return response()->json("Created Successfully",201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TeamMembers $teamMembers)
    {
        //
        $teamMember = TeamMembers::findOrFail($teamMembers->id);
        return response()->json($teamMember);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TeamMembers $teamMembers)
    {
        //
        $teamMember = TeamMembers::findOrFail($teamMembers->id);
        $teamMember->update($request->all());
        return response()->json("Updated Successfully",200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        TeamMembers::destroy($id);
        return response()->json('Deleted Successfully',204);
    }
}
