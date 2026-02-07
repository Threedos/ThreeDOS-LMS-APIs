<?php

namespace App\Services;

use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    /**
     * Send a message to Gemini AI and get the response (Railway-friendly).
     *
     * @param string $message
     * @return string
     */
    public function chat(string $message): string
    {
        try {
            // Railway limits HTTP requests to ~15s
            // Make sure PHP does not time out earlier
            set_time_limit(15);
            ini_set('default_socket_timeout', 15);

            // Limit max_tokens so AI responds quickly
            $result = Gemini::generativeModel('gemini-2.5-flash')
                ->generateContent($message, timeout: 15, max_tokens: 120);

            $aiText = $result->text();

            // Log response length for monitoring
            Log::info('Gemini response length: ' . strlen($aiText));

            return $aiText;

        } catch (\Exception $e) {
            // Log full error details for production debugging
            Log::error('Gemini Exception on Railway: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'code' => $e->getCode(),
                'message_sent' => $message,
            ]);

            // Return Railway-friendly fallback message
            return 'Sorry, the AI could not respond in time. Please try again with a shorter question.';
        }
    }
}
