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
     * @OA\Post(
     *     path="/ai/prompt",
     *     summary="Enviar prompt para IA",
     *     description="Envia um prompt diretamente para o provedor de IA escolhido (OpenAI ou Google Gemini). Requer autenticacao e token da API do provedor.",
     *     operationId="sendPrompt",
     *     tags={"AI Prompts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados do prompt para envio",
     *         @OA\JsonContent(
     *             required={"provider","api_token","prompt"},
     *             @OA\Property(property="provider", type="string", enum={"openai", "gemini"}, example="openai", description="Provedor de IA a ser usado"),
     *             @OA\Property(property="api_token", type="string", example="sk-proj-abc123...", description="Token de API do provedor escolhido"),
     *             @OA\Property(property="prompt", type="string", example="Explique o que e inteligencia artificial em 100 palavras", minLength=10, description="Texto do prompt (minimo 10 caracteres)"),
     *             @OA\Property(property="temperature", type="number", format="float", example=0.7, minimum=0, maximum=1, description="Controle de criatividade (0=deterministico, 1=criativo)"),
     *             @OA\Property(property="max_tokens", type="integer", example=1000, minimum=100, maximum=4000, description="Limite maximo de tokens na resposta")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resposta da IA recebida com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="response", type="string", example="Inteligencia artificial e a simulacao de processos de inteligencia humana por sistemas computacionais..."),
     *             @OA\Property(property="provider", type="string", example="openai")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validacao",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The provider field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao processar prompt",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Erro ao processar prompt: OpenAI API Error")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Nao autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
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
