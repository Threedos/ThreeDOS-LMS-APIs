<?php

namespace App\Services;

use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    /**
     * Send a message to Gemini AI and get the response.
     *
     * @param string $message
     * @return string
     */
    public function chat(string $message): string
    {
        try {
            // Increase PHP execution time for long AI responses
            set_time_limit(60); // 60 seconds
            ini_set('default_socket_timeout', 60);

            // Make the API request with a proper timeout
            $result = Gemini::generativeModel(model: 'gemini-2.5-flash')
                ->generateContent($message, timeout: 60); // 60s timeout

            $aiText = $result->text();

            // Optional: log AI responses for debugging or monitoring
            Log::info('Gemini response length: ' . strlen($aiText));

            return $aiText;

        } catch (\Exception $e) {
            // Log full error for debugging in production
            Log::error('Gemini Service Exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'message' => $message,
            ]);

            return 'Sorry, I am having trouble connecting to the AI service right now.';
        }
    }
}
