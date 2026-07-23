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

    /**
     * AI Chat
     *
     * Send a message to the Gemini AI and receive a response.
     *
     * @tags AI Chat
     * @response 200 scenario="Success" {"status": "success", "message": "AI response retrieved successfully.", "data": "The AI response text..."}
     */
    public function chat(Request $request)
    {
        $request->validate([
            // 'session_id' => 'required|string|max:1000|exists:council_sessions,id',
            'message' => 'required|string|max:1000',
        ]);

        $response = $this->geminiService->chat($request->message);

        return $this->successResponse($response, 'AI response retrieved successfully.');
    }
}
