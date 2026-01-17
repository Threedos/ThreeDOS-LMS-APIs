<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
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
        $this->authorize('viewAny', TeamMember::class);
        return response()->json(
            TeamMember::all(),
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', TeamMember::class);
        $teamMember = TeamMember::create(
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
    public function show(TeamMember $teamMember)
    {
        $this->authorize('view', TeamMember::class);
        return response()->json($teamMember, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TeamMember $teamMember)
    {
        $this->authorize('update', TeamMember::class);
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
        $this->authorize('delete', TeamMember::class);
        TeamMember::destroy($id);

        return response()->json(null, 204);
    }
}
