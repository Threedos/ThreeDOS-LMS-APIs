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
        return $this->successResponse(
            TeamMember::all(),
            'Success'
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

        return $this->createdResponse(
            ['count' => count($membersData)],
            'Success'
        );
    }


    /**
     * Display the specified resource.
     */
    public function show(TeamMember $teamMember)
    {
        $this->authorize('view', TeamMember::class);
        return $this->successResponse($teamMember, 'Success');
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

        return $this->successResponse($teamMember, 'Success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete', TeamMember::class);
        TeamMember::destroy($id);

        return $this->noContentResponse('Success');
    }
}
