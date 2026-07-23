<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\BulkTeamMemberRequest;
use App\Services\TeamMemberService;
use App\Services\CacheService;
use App\Http\Requests\TeamMemberRequest;
use App\Http\Requests\UpdateTeamMemberRequest;
class TeamMemberController extends Controller
{
    use AuthorizesRequests;
    protected $teamMemberService;
    protected $cacheService;

    public function __construct(TeamMemberService $teamMemberService, CacheService $cacheService)
    {
        $this->teamMemberService = $teamMemberService;
        $this->cacheService = $cacheService;
    }

    /**
     * Display a listing of the resource.
     */
    /**
     * List Team Members
     *
     * Retrieve all team members.
     *
     * @tags Team Members
     * @response 200 scenario="Success" {"status": "success", "message": "Team members retrieved successfully", "data": []}
     */
    public function index()
    {
        $this->authorize('viewAny', TeamMember::class);
        $members = $this->teamMemberService->getAllTeamMembers();

        return $this->successResponse(
            $members,
            'Team members retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Add Team Member
     *
     * Add a user to a team.
     *
     * @tags Team Members
     * @response 201 scenario="Created" {"status": "success", "message": "Team member created successfully", "data": null}
     * @response 403 scenario="Forbidden" {"status": "error", "message": "Unauthorized"}
     */
    public function store(TeamMemberRequest $request)
    {
        $this->authorize('create', TeamMember::class);
        $this->teamMemberService->createTeamMember($request->all());

        // Clear team and team members cache
        $this->cacheService->clearResourceCache('team-members');
        $this->cacheService->clearResourceCache('teams');

        return $this->createdResponse(
            null,
            'Team member created successfully'
        );
    }
    /**
     * Bulk Add Team Members
     *
     * Add multiple users to teams in a single request.
     *
     * @tags Team Members
     * @response 201 scenario="Created" {"status": "success", "message": "Team members created successfully", "data": {"count": 5}}
     * @response 403 scenario="Forbidden" {"status": "error", "message": "Unauthorized"}
     */
    public function storeBulk(BulkTeamMemberRequest $request)
    {
        $this->authorize('create', TeamMember::class);
        $validated = $request->validated();
        $this->teamMemberService->bulkCreateTeamMembers($validated['members']);

        // Clear team and team members cache
        $this->cacheService->clearResourceCache('team-members');
        $this->cacheService->clearResourceCache('teams');

        return $this->createdResponse(
            ['count' => count($validated['members'])],
            'Team members created successfully'
        );
    }

    /**
     * Display the specified resource.
     */
    /**
     * Get Team Member
     *
     * Retrieve a specific team member record by its ID.
     *
     * @tags Team Members
     * @response 200 scenario="Success" {"status": "success", "message": "Team member retrieved successfully", "data": {}}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
     */
    public function show(string $id)
    {
        $this->authorize('view', TeamMember::class);
        $member = $this->teamMemberService->getTeamMemberById($id);

        return $this->successResponse(
            $member,
            'Team member retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update Team Member
     *
     * Update the role or team assignment of a team member.
     *
     * @tags Team Members
     * @response 200 scenario="Success" {"status": "success", "message": "Team member updated successfully", "data": {}}
     * @response 403 scenario="Forbidden" {"status": "error", "message": "Unauthorized"}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
     */
    public function update(UpdateTeamMemberRequest $request, string $id)
    {
        $teamMember = TeamMember::findOrFail($id);
        $this->authorize('update', $teamMember);
        $member = $this->teamMemberService->updateTeamMember($id, $request->validated());

        // Clear team and team member cache
        $this->cacheService->forget("team-member:{$id}");
        $this->cacheService->clearResourceCache('team-members');
        $this->cacheService->clearResourceCache('teams');

        return $this->successResponse($member, 'Team member updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove Team Member
     *
     * Remove a user from a team.
     *
     * @tags Team Members
     * @response 204 scenario="No Content" {}
     * @response 403 scenario="Forbidden" {"status": "error", "message": "Unauthorized"}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
     */
    public function destroy(string $id)
    {
        $teamMember = TeamMember::findOrFail($id);

        // Pass the model instance, not the class
        $this->authorize('delete', $teamMember);

        $this->teamMemberService->deleteTeamMember($id);

        // Clear team and team member cache
        $this->cacheService->forget("team-member:{$id}");
        $this->cacheService->clearResourceCache('team-members');
        $this->cacheService->clearResourceCache('teams');

        return $this->noContentResponse('Team member deleted successfully');
    }

}
