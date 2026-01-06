<?php

namespace App\Http\Controllers;

use App\Http\Requests\SessionRequests\StoreSessionRequest;
use App\Http\Requests\SessionRequests\UpdateSessionRequest;
use App\Models\CouncilSession;
use App\Http\Resources\SessionResource;
use App\Http\Requests\SessionRequests\PaginatedSessionRequest;

class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PaginatedSessionRequest $request)
    {
        //
        $council_id = $request->user()->council_id;
        $baseQuery= CouncilSession::query();
        $baseQuery= $baseQuery->where('council_id',$council_id);
        if ($request->search) {
            $baseQuery= $baseQuery->where('title','like',"%{$request->search}%");
        }

        $sessions = $baseQuery->paginate($request->pageSize, ['*'], 'pageIndex', $request->pageIndex);
        return SessionResource::collection($sessions);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSessionRequest $request)
    {
        //  
        $session = CouncilSession::create($request->validated());
        return response()->json($session, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $session = CouncilSession::findOrFail($id);
        return response()->json($session);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSessionRequest $request, string $id)
    {
        //
        $session = CouncilSession::findOrFail($id);
        $session->update($request->validated());
        return response()->json($session);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $session = CouncilSession::findOrFail($id);
        $session->delete();
        return response()->json(null, 204);
    }
}
