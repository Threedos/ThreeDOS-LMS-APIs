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
    public function index(AllCouncilRequest $request)
    {
        $this->authorize('viewAny', Council::class);

        $pageIndex = $request->input('pageIndex');
        $pageSize = $request->input('pageSize');
        $search = $request->input('search', '');

        // $cacheKey = "councils:page_{$pageIndex}:size_{$pageSize}:search_{$search}";

        // Use Redis cache service
        return $this->successResponse(
            // $this->cacheService->remember($cacheKey, 3600, function () use ($request) {
                CouncilResource::collection($this->councilService->getAllCouncils($request))
            // }),
            ,
            'Councils retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
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
    public function show(string $id)
    {
        $cacheKey = "council:{$id}";

        // Use Redis cache service with remember pattern
        return $this->successResponse(
            $this->cacheService->remember($cacheKey, 3600, function () use ($id) {
                $council = Council::findOrFail($id);
                $this->authorize('view', $council);
                return $this->councilService->getCouncilById($id);
            }),
            'Council retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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
