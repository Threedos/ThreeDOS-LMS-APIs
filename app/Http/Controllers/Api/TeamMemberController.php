<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\TeamMemberRequest;
use App\Services\TeamMemberService;

class TeamMemberController extends Controller
{
    use AuthorizesRequests;
    protected $teamMemberService;

    public function __construct(TeamMemberService $teamMemberService)
    {
        $this->teamMemberService = $teamMemberService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', TeamMember::class);
        $members = $this->teamMemberService->getAllTeamMembers();
        return $this->successResponse($members, 'Team members retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TeamMemberRequest $request)
    {
        $this->authorize('create', TeamMember::class);
        $validated = $request->validated();
        $this->teamMemberService->bulkCreateTeamMembers($validated['members']);

        return $this->createdResponse(
            ['count' => count($validated['members'])],
            'Team members created successfully'
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('view', TeamMember::class);
        $member = $this->teamMemberService->getTeamMemberById($id);
        return $this->successResponse($member, 'Team member retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorize('update', TeamMember::class);
        $member = $this->teamMemberService->updateTeamMember($id, $request->only(['rate', 'role', 'task']));
        return $this->successResponse($member, 'Team member updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete', TeamMember::class);
        $this->teamMemberService->deleteTeamMember($id);

        return $this->noContentResponse('Team member deleted successfully');
    }
}
