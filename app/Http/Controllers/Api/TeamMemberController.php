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
    public function index()
    {
        $this->authorize('viewAny', TeamMember::class);
        $cacheKey = "team-members:all";
        return $this->successResponse(
            $this->cacheService->remember($cacheKey, 3600, function () {
                return $this->teamMemberService->getAllTeamMembers();
            }),
            'Team members retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
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
    public function show(string $id)
    {
        $this->authorize('view', TeamMember::class);
        $cacheKey = "team-member:{$id}";
        return $this->successResponse(
            $this->cacheService->remember($cacheKey, 3600, function () use ($id) {
                return $this->teamMemberService->getTeamMemberById($id);
            }),
            'Team member retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorize('update', TeamMember::class);
        $member = $this->teamMemberService->updateTeamMember($id, $request->only(['rate', 'role', 'task']));

        // Clear team and team member cache
        $this->cacheService->forget("team-member:{$id}");
        $this->cacheService->clearResourceCache('team-members');
        $this->cacheService->clearResourceCache('teams');

        return $this->successResponse($member, 'Team member updated successfully');
    }

    /**
     * Remove the specified resource from storage.
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
