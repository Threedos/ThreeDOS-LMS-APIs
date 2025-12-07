<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Services\CouncilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;



class CouncilController extends Controller
{
    protected $councilService;

    public function __construct(CouncilService $councilService)
    {
        $this->councilService = $councilService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json($this->councilService->getAllCouncils());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            // 'head_id' => 'required',
            // 'instructor_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
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
