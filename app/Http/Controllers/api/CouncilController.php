<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Http\Requests\CouncilRequests\AllCouncilRequest;
use App\Http\Requests\CouncilRequests\CouncilCreateRequest;
use App\Services\CouncilService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Council;
// use Illuminate\Support\Facades\Cache;



class CouncilController extends Controller
{
    use AuthorizesRequests;

    protected $councilService;

    public function __construct(CouncilService $councilService)
    {
        $this->councilService = $councilService;
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

        // $cacheKey = "councils:page_{$pageIndex}:size_{$pageSize}:search_{$search}";

        // $councils = Cache::tags(['councils'])->remember($cacheKey, 3600, function () use ($request) {
        //     return $this->councilService->getAllCouncils($request);
        // });
        $councils = $this->councilService->getAllCouncils($request);
        return response()->json($councils);
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

        // Cache::tags(['councils'])->flush();

        return response()->json(['message' => 'Council created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $council = Council::findOrFail($id);
        $this->authorize('view', $council);
        return response()->json($this->councilService->getCouncilById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $council = Council::findOrFail($id);
        $this->authorize('update', $council);
        $this->councilService->updateCouncil($id, $request->all());
        // Cache::tags(['councils'])->flush();
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
        // Cache::tags(['councils'])->flush();
        return response()->json(['message' => 'Council deleted successfully']);
    }
}
