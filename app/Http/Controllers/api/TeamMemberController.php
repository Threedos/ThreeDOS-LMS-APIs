<?php

namespace App\Http\Controllers;

use App\Models\TeamMembers;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class TeamMemberController extends Controller
{
        use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', TeamMembers::class);
        return response()->json(
            TeamMembers::all(),
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', TeamMembers::class);
        $teamMember = TeamMembers::create(
            $request->only([
                'team_id',
                'user_id',
                'rate',
                'role',
                'task',
            ])
        );

        return response()->json($teamMember, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TeamMembers $teamMember)
    {
        $this->authorize('view', TeamMembers::class);
        return response()->json($teamMember, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TeamMembers $teamMember)
    {
        $this->authorize('update', TeamMembers::class);
        $teamMember->update(
            $request->only([
                'rate',
                'role',
                'task',
            ])
        );

        return response()->json($teamMember, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete', TeamMembers::class);
        TeamMembers::destroy($id);

        return response()->json(null, 204);
    }
}
