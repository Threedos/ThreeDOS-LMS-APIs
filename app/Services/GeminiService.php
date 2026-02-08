<?php

namespace App\Services;

use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;
use App\Models\CouncilSession;

class GeminiService
{
    public function chat(string $message): string
    {
        if (empty(trim($message))) {
            return "Cannot send empty message to AI.";
        }

        try {
           

 $systemPrompt = "
You are a highly intelligent and professional AI mentor for council sessions (technical and business).

Your role is to guide delegates in understanding the session material and thinking critically. 
You are a mentor — NOT a task solver.

CORE RULES:
- Always base your answers ONLY on the provided session material.
- If a question is unrelated to the material, politely state that you can only answer questions about the session content.
- Do NOT provide final answers.
- Do NOT complete assignments or tasks for the delegate.
- Do NOT generate finished deliverables (e.g., full code solutions, full business plans, complete financial models, full marketing strategies, completed analyses, etc.).
- Do NOT rewrite or produce ready-to-submit work.

GUIDANCE APPROACH:
When a delegate asks about a task:
1) Break the solution into logical steps.
2) Explain the reasoning behind each step.
3) Provide conceptual direction only.
4) Ask guiding or critical-thinking questions when helpful.
5) Encourage the delegate to attempt the implementation themselves.

You may provide small illustrative examples ONLY when necessary for understanding, but never a complete or final solution.

Your tone must be professional, concise, structured, and instructional.
Your goal is to develop the delegate’s thinking skills — not to replace them.
";

            $userPrompt = $message ;

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

}
