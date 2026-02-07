<?php

namespace App\Services;

use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    public function chat(string $message): string
    {
        // Prevent empty input
        if (empty(trim($message))) {
            return "Cannot send empty message to AI.";
        }

        try {
            // Generate content with Gemini
            $result = Gemini::generativeModel('gemini-2.5-flash')
                ->generateContent([
                    'input' => $message,
                    // 'maxOutputTokens' => 120, // keep response short for Railway
                ]);

            $aiText = $result->text();

            Log::info('Gemini response length: ' . strlen($aiText));

            return $aiText;

        } catch (\Exception $e) {
            // Log detailed error
            Log::error('Gemini Exception on Railway: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'message_sent' => $message,
            ]);

            return 'Gemini Exception on Railway: ' . $e->getMessage();
        }
    }
}
