<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\CouncilRequests\AllCouncilRequest;
use App\Http\Requests\CouncilRequests\CouncilCreateRequest;
use App\Services\CouncilService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Council;
use Illuminate\Support\Facades\Cache;
use App\Services\CacheService;
use App\Http\Resources\CouncilResource;
use App\Http\Requests\CouncilRequests\UpdateCouncilRequest;

class CouncilController extends Controller
{
    use AuthorizesRequests;

    protected $councilService;
    protected $cacheService;

    public function __construct(CouncilService $councilService, CacheService $cacheService)
    {
        $this->councilService = $councilService;
        $this->cacheService = $cacheService;
    }

    /**
     * Display a listing of the resource.
     */
    /**
     * List Councils
     *
     * Retrieve all councils accessible to the authenticated user.
     *
     * @tags Councils
     * @response 200 scenario="Success" {"status": "success", "message": "Councils retrieved successfully", "data": []}
     */
    public function index(AllCouncilRequest $request)
    {
        $this->authorize('viewAny', Council::class);

        return $this->successResponse(
            CouncilResource::collection($this->councilService->getAllCouncils($request)),
            'Councils retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Create Council
     *
     * Create a new council with a name and description.
     *
     * @tags Councils
     * @response 201 scenario="Created" {"status": "success", "message": "Council created successfully", "data": null}
     * @response 403 scenario="Forbidden" {"status": "error", "message": "Unauthorized"}
     */
    public function store(CouncilCreateRequest $request)
    {
        $this->authorize('create', Council::class);
        $council = $this->councilService->createCouncil([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Clear council cache and dependencies after creating
        $this->cacheService->clearResourceCache('councils');
        $this->cacheService->clearResourceCache('tasks');
        $this->cacheService->clearResourceCache('teams');
        $this->cacheService->clearResourceCache('sessions');

        return $this->createdResponse(null, 'Council created successfully');
    }

    /**
     * Display the specified resource.
     */
    /**
     * Get Council
     *
     * Retrieve a specific council by its ID.
     *
     * @tags Councils
     * @response 200 scenario="Success" {"status": "success", "message": "Council retrieved successfully", "data": {}}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
     */
    public function show(string $id)
    {
        $council = Council::findOrFail($id);
        $this->authorize('view', $council);

        return $this->successResponse(
            $this->councilService->getCouncilById($id),
            'Council retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update Council
     *
     * Update the name or description of an existing council.
     *
     * @tags Councils
     * @response 200 scenario="Success" {"status": "success", "message": "Success", "data": null}
     * @response 403 scenario="Forbidden" {"status": "error", "message": "Unauthorized"}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
     */
    public function update(UpdateCouncilRequest $request, string $id)
    {
        $council = Council::findOrFail($id);
        $this->authorize('update', $council);
        $this->councilService->updateCouncil($id, $request->all());

        // Clear specific council cache, council list cache and dependencies
        $this->cacheService->forget("council:{$id}");
        $this->cacheService->clearResourceCache('councils');
        $this->cacheService->clearResourceCache('tasks');
        $this->cacheService->clearResourceCache('teams');
        $this->cacheService->clearResourceCache('sessions');

        return $this->successResponse(null, 'Success');
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Delete Council
     *
     * Permanently delete a council by its ID.
     *
     * @tags Councils
     * @response 200 scenario="Success" {"status": "success", "message": "Success", "data": null}
     * @response 403 scenario="Forbidden" {"status": "error", "message": "Unauthorized"}
     * @response 404 scenario="Not found" {"status": "error", "message": "Not Found"}
     */
    public function destroy(string $id)
    {
        $council = Council::findOrFail($id);
        $this->authorize('delete', $council);
        $this->councilService->deleteCouncil($id);

        // Clear specific council cache, council list cache and dependencies
        $this->cacheService->forget("council:{$id}");
        $this->cacheService->clearResourceCache('councils');
        $this->cacheService->clearResourceCache('tasks');
        $this->cacheService->clearResourceCache('teams');
        $this->cacheService->clearResourceCache('sessions');

        return $this->successResponse(null, 'Success');
    }
}
