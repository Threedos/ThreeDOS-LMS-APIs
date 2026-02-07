<?php

namespace App\Services;

use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    public function chat(string $message)
    {
        try {
            $result = Gemini::generativeModel(model: 'gemini-2.5-flash')->generateContent($message);
            return $result->text();
        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            return 'Sorry, I am having trouble connecting to the AI service right now.';
        }
    }
}
