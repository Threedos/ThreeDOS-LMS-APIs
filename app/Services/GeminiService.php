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

            $result = Gemini::generativeModel('gemini-2.5-flash')
                ->generateContent([
                    'input' => $message,
                    'maxOutputTokens' => 120, // keep response short
                ], [
                    'timeout' => 15            // HTTP client timeout
                ]);
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
            return 'Gemini Exception on Railway: ' . $e->getMessage();
        }
    }
}
