<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AiPromptController extends Controller
{
    /**
     * Send prompt to AI provider
     */
    public function sendPrompt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|in:openai,gemini',
            'api_token' => 'required|string',
            'prompt' => 'required|string|min:10',
            'temperature' => 'nullable|numeric|min:0|max:1',
            'max_tokens' => 'nullable|integer|min:100|max:4000',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $provider = $request->provider;
        $apiToken = $request->api_token;
        $prompt = $request->prompt;
        $temperature = $request->temperature ?? 0.7;
        $maxTokens = $request->max_tokens ?? 1000;

        try {
            switch ($provider) {
                case 'openai':
                    $response = $this->sendToOpenAI($apiToken, $prompt, $temperature, $maxTokens);
                    break;
                case 'gemini':
                    $response = $this->sendToGemini($apiToken, $prompt, $temperature, $maxTokens);
                    break;
                default:
                    return response()->json(['error' => 'Provider não suportado'], 400);
            }

            return response()->json([
                'success' => true,
                'response' => $response,
                'provider' => $provider
            ]);

        } catch (\Exception $e) {
            Log::error("AI Prompt Error ({$provider}): " . $e->getMessage());
            return response()->json([
                'error' => 'Erro ao processar prompt: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send to OpenAI API
     */
    private function sendToOpenAI($apiToken, $prompt, $temperature, $maxTokens)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiToken,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
        ]);

        if ($response->failed()) {
            throw new \Exception('OpenAI API Error: ' . $response->body());
        }

        $data = $response->json();
        return $data['choices'][0]['message']['content'] ?? 'Sem resposta';
    }

    /**
     * Send to Google Gemini API
     */
    private function sendToGemini($apiToken, $prompt, $temperature, $maxTokens)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key={$apiToken}", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => $temperature,
                'maxOutputTokens' => $maxTokens,
            ]
        ]);

        if ($response->failed()) {
            throw new \Exception('Gemini API Error: ' . $response->body());
        }

        $data = $response->json();
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Sem resposta';
    }
}
