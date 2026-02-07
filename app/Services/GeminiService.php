<?php

namespace App\Services;

use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;
use App\Models\CouncilSession;

class GeminiService
{
    public function chat(string $message, string $session_id): string
    {
        if (empty(trim($message))) {
            return "Cannot send empty message to AI.";
        }

        try {
            $material = $this->getCouncilSessionMaterial($session_id);

            $systemPrompt = "You are a highly intelligent and professional AI assistant for council sessions. 
                Your role is to help delegates understand the session material, answer questions clearly and politely, and provide concise and accurate explanations. 
                Always base your answers only on the provided session material. 
                If the question is unrelated to the material, respond politely that you can only answer questions about the session content.
                Provide examples or step-by-step guidance when it helps understanding, but keep your responses short and focused.";

            $userPrompt = $message . "\n\n" . $material;

            $result = Gemini::generativeModel('gemini-2.5-flash')
                ->generateContent($systemPrompt . "\n\n" . $userPrompt);

            $aiText = $result->text();

            Log::info('Gemini response length: ' . strlen($aiText));

            return $aiText;

        } catch (\Exception $e) {
            Log::error('Gemini Exception on Railway: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'message_sent' => $systemPrompt . "\n\n" . $userPrompt,
            ]);

            return 'Gemini Exception on Railway: ' . $e->getMessage();
        }
    }

    public function getCouncilSessionMaterial(string $session_id): string
    {
        $councilSession = CouncilSession::findOrFail($session_id);
        return $councilSession->material;
    }
}
