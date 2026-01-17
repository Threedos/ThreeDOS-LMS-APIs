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
        // return response()->json("hello");
        $this->authorize('viewAny', Council::class);

        $pageIndex = $request->input('pageIndex');
        $pageSize = $request->input('pageSize');
        $search = $request->input('search', '');

        $cacheKey = "councils:page_{$pageIndex}:size_{$pageSize}:search_{$search}";

        // Use Redis cache service
        return response()->json(
            $this->cacheService->remember($cacheKey, 3600, function () use ($request) {
                return $this->councilService->getAllCouncils($request);
            })
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
            // 'head_id' => $request->head_id,
            // 'instructor_id' => $request->instructor_id,
        ]);

        // Clear council cache after creating
        $this->cacheService->clearResourceCache('councils');

        return response()->json(['message' => 'Council created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cacheKey = "council:{$id}";

        // Use Redis cache service with remember pattern
        return response()->json(
            $this->cacheService->remember($cacheKey, 3600, function () use ($id) {
                $council = Council::findOrFail($id);
                $this->authorize('view', $council);
                return $this->councilService->getCouncilById($id);
            })
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

        // Clear specific council cache and council list cache
        $this->cacheService->forget("council:{$id}");
        $this->cacheService->clearResourceCache('councils');

        return response()->json(['message' => 'Council updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $council = Council::findOrFail($id);
        $this->authorize('delete', $council);
        $this->councilService->deleteCouncil($id);

        // Clear specific council cache and council list cache
        $this->cacheService->forget("council:{$id}");
        $this->cacheService->clearResourceCache('councils');

        return response()->json(['message' => 'Council deleted successfully']);
    }
}
