<?php

namespace App\Http\Controllers;

use App\Models\AiAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AiAgentController extends Controller
{
    public function __construct()
    {
        // Middleware já aplicado nas rotas
    }

    public function index()
    {
        $agents = auth('api')->user()->aiAgents()->latest()->get();
        return response()->json($agents);
    }

    /**
     * Create a new agent
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ai_provider' => 'required|in:openai,gemini',
            'ai_model' => 'nullable|string',
            'system_prompt' => 'nullable|string',
            'instructions' => 'nullable|string',
            'configuration' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $agent = auth('api')->user()->aiAgents()->create($request->all());

        return response()->json([
            'message' => 'Agente criado com sucesso!',
            'agent' => $agent
        ], 201);
    }

    /**
     * Get a specific agent
     */
    public function show($id)
    {
        $agent = auth('api')->user()->aiAgents()->findOrFail($id);
        return response()->json($agent);
    }

    /**
     * Update an agent
     */
    public function update(Request $request, $id)
    {
        $agent = auth('api')->user()->aiAgents()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'ai_provider' => 'sometimes|required|in:openai,gemini',
            'ai_model' => 'nullable|string',
            'system_prompt' => 'nullable|string',
            'instructions' => 'nullable|string',
            'configuration' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $agent->update($request->all());

        return response()->json([
            'message' => 'Agente atualizado com sucesso!',
            'agent' => $agent
        ]);
    }

    /**
     * Delete an agent
     */
    public function destroy($id)
    {
        $agent = auth('api')->user()->aiAgents()->findOrFail($id);
        $agent->delete();

        return response()->json([
            'message' => 'Agente deletado com sucesso!'
        ]);
    }

    /**
     * Send prompt to a specific agent
     */
    public function sendPrompt(Request $request, $id)
    {
        $agent = auth('api')->user()->aiAgents()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'api_token' => 'required|string',
            'prompt' => 'required|string|min:10',
            'file_content' => 'nullable|string',
            'additional_data' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        // Build the complete prompt with agent context
        $fullPrompt = $this->buildAgentPrompt($agent, $request);

        // Use the AiPromptController to send the request
        $aiController = new \App\Http\Controllers\AiPromptController();
        
        $config = $agent->configuration ?? [];
        $temperature = $config['temperature'] ?? 0.7;
        $maxTokens = $config['max_tokens'] ?? 1000;

        try {
            $response = $this->sendToAI(
                $agent->ai_provider,
                $request->api_token,
                $fullPrompt,
                $temperature,
                $maxTokens
            );

            return response()->json([
                'success' => true,
                'response' => $response,
                'agent' => $agent->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao processar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Build complete prompt with agent context
     */
    private function buildAgentPrompt($agent, $request)
    {
        $parts = [];

        if ($agent->system_prompt) {
            $parts[] = "CONTEXTO DO AGENTE:\n" . $agent->system_prompt;
        }

        if ($agent->instructions) {
            $parts[] = "\nINSTRUÇÕES:\n" . $agent->instructions;
        }

        if ($request->file_content) {
            $parts[] = "\nARQUIVO/DADOS FORNECIDOS:\n" . $request->file_content;
        }

        if ($request->additional_data) {
            $parts[] = "\nDADOS ADICIONAIS:\n" . $request->additional_data;
        }

        $parts[] = "\nPROMPT DO USUÁRIO:\n" . $request->prompt;

        return implode("\n\n", $parts);
    }

    /**
     * Send to AI provider
     */
    private function sendToAI($provider, $apiToken, $prompt, $temperature, $maxTokens)
    {
        switch ($provider) {
            case 'openai':
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'temperature' => $temperature,
                    'max_tokens' => $maxTokens,
                ]);
                if ($response->failed()) throw new \Exception('OpenAI Error: ' . $response->body());
                return $response->json()['choices'][0]['message']['content'] ?? 'Sem resposta';

            case 'gemini':
                $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key={$apiToken}", [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['temperature' => $temperature, 'maxOutputTokens' => $maxTokens],
                ]);
                if ($response->failed()) throw new \Exception('Gemini Error: ' . $response->body());
                return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'Sem resposta';

            default:
                throw new \Exception('Provider não suportado');
        }
    }
}
