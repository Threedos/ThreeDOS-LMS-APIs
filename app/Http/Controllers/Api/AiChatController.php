<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class AiChatController extends Controller
{
    use ApiResponse;

    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function chat(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string|max:1000',
            'material' => 'required|string|max:1000',
            'message' => 'required|string|max:1000',
        ]);

        $response = $this->geminiService->chat($request->message);

        return $this->successResponse($response, 'AI response retrieved successfully.');
    }
}
