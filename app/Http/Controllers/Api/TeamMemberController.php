<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\TeamMemberRequest;
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
    public function store(TeamMemberRequest $request)
    {
        $this->authorize('create', TeamMember::class);

        $validated = $request->validated();

        $membersData = $validated['members'];

        // Add timestamps if using $table->timestamps()
        foreach ($membersData as &$member) {
            $member['created_at'] = now();
            $member['updated_at'] = now();
        }

        // Bulk insert
        TeamMember::insert($membersData);

        return response()->json([
            'message' => 'Team members created successfully',
            'count' => count($membersData),
        ], 201);
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
