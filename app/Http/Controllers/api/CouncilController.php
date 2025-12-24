<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Http\Requests\CouncilRequests\AllCouncilRequest;
use App\Http\Requests\CouncilRequests\CouncilCreateRequest;
use App\Services\CouncilService;    
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;



class CouncilController extends Controller
{
        use AuthorizesRequests  ;

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
        // $this->authorize('viewAny', Council::class);

        return response()->json($this->councilService->getAllCouncils($request));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CouncilCreateRequest $request)
    {
        
        $council = $this->councilService->createCouncil([
            'name' => $request->name,
            'description' => $request->description,
            // 'head_id' => $request->head_id,
            // 'instructor_id' => $request->instructor_id,
        ]);

        return response()->json(['message' => 'Council created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json($this->councilService->getCouncilById($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->councilService->updateCouncil($id, $request->all());
        return response()->json(['message' => 'Council updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->councilService->deleteCouncil($id);
        return response()->json(['message' => 'Council deleted successfully']);
    }
}
