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

    /**
     * @OA\Get(
     *     path="/agents",
     *     summary="Listar agentes de IA",
     *     description="Retorna todos os agentes de IA do usuario autenticado ordenados por data de criacao (mais recentes primeiro).",
     *     operationId="listAgents",
     *     tags={"AI Agents"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de agentes retornada com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Assistente de Codigo Python"),
     *                 @OA\Property(property="description", type="string", example="Agente especializado em revisar e otimizar codigo Python"),
     *                 @OA\Property(property="ai_provider", type="string", example="openai"),
     *                 @OA\Property(property="ai_model", type="string", nullable=true, example="gpt-4"),
     *                 @OA\Property(property="system_prompt", type="string", nullable=true, example="Voce e um especialista em Python..."),
     *                 @OA\Property(property="instructions", type="string", nullable=true, example="Sempre siga as boas praticas PEP 8..."),
     *                 @OA\Property(property="configuration", type="object", nullable=true, example={"temperature": 0.5, "max_tokens": 2000}),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
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
    public function index()
    {
        $agents = auth('api')->user()->aiAgents()->latest()->get();
        return response()->json($agents);
    }

    /**
     * @OA\Post(
     *     path="/agents",
     *     summary="Criar novo agente de IA",
     *     description="Cria um agente de IA personalizado com instrucoes e configuracoes especificas para o usuario autenticado.",
     *     operationId="createAgent",
     *     tags={"AI Agents"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados do agente",
     *         @OA\JsonContent(
     *             required={"name","ai_provider"},
     *             @OA\Property(property="name", type="string", example="Assistente de Codigo Python", description="Nome do agente"),
     *             @OA\Property(property="description", type="string", nullable=true, example="Agente especializado em revisar codigo Python", description="Descricao do agente"),
     *             @OA\Property(property="ai_provider", type="string", enum={"openai", "gemini"}, example="openai", description="Provedor de IA"),
     *             @OA\Property(property="ai_model", type="string", nullable=true, example="gpt-4", description="Modelo especifico da IA"),
     *             @OA\Property(property="system_prompt", type="string", nullable=true, example="Voce e um especialista em Python...", description="Prompt de sistema para contexto"),
     *             @OA\Property(property="instructions", type="string", nullable=true, example="Sempre siga PEP 8...", description="Instrucoes adicionais"),
     *             @OA\Property(property="configuration", type="object", nullable=true, example={"temperature": 0.5, "max_tokens": 2000}, description="Configuracoes personalizadas")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Agente criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Agente criado com sucesso!"),
     *             @OA\Property(property="agent", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Assistente de Codigo Python"),
     *                 @OA\Property(property="ai_provider", type="string", example="openai"),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validacao",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The name field is required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Nao autenticado"
     *     )
     * )
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
     * @OA\Get(
     *     path="/agents/{id}",
     *     summary="Obter agente especifico",
     *     description="Retorna os detalhes completos de um agente de IA especifico do usuario autenticado.",
     *     operationId="getAgent",
     *     tags={"AI Agents"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do agente",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Agente retornado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Assistente de Codigo Python"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="ai_provider", type="string", example="openai"),
     *             @OA\Property(property="system_prompt", type="string"),
     *             @OA\Property(property="configuration", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Agente nao encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\AiAgent]")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Nao autenticado"
     *     )
     * )
     */
    public function show($id)
    {
        $agent = auth('api')->user()->aiAgents()->findOrFail($id);
        return response()->json($agent);
    }

    /**
     * @OA\Put(
     *     path="/agents/{id}",
     *     summary="Atualizar agente",
     *     description="Atualiza as informacoes de um agente de IA existente do usuario autenticado.",
     *     operationId="updateAgent",
     *     tags={"AI Agents"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do agente",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Campos a serem atualizados",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Assistente de Codigo Python Avancado"),
     *             @OA\Property(property="description", type="string", nullable=true),
     *             @OA\Property(property="ai_provider", type="string", enum={"openai", "gemini"}),
     *             @OA\Property(property="ai_model", type="string", nullable=true),
     *             @OA\Property(property="system_prompt", type="string", nullable=true),
     *             @OA\Property(property="instructions", type="string", nullable=true),
     *             @OA\Property(property="configuration", type="object", nullable=true),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Agente atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Agente atualizado com sucesso!"),
     *             @OA\Property(property="agent", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Agente nao encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validacao"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Nao autenticado"
     *     )
     * )
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
     * @OA\Delete(
     *     path="/agents/{id}",
     *     summary="Deletar agente",
     *     description="Remove permanentemente um agente de IA do usuario autenticado.",
     *     operationId="deleteAgent",
     *     tags={"AI Agents"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do agente",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Agente deletado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Agente deletado com sucesso!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Agente nao encontrado"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Nao autenticado"
     *     )
     * )
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
     * @OA\Post(
     *     path="/agents/{id}/prompt",
     *     summary="Enviar prompt para agente especifico",
     *     description="Envia um prompt para um agente de IA especifico. O agente aplica seu system_prompt, instrucoes e configuracoes ao processar o prompt.",
     *     operationId="sendPromptToAgent",
     *     tags={"AI Agents"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do agente",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados do prompt",
     *         @OA\JsonContent(
     *             required={"api_token","prompt"},
     *             @OA\Property(property="api_token", type="string", example="sk-proj-abc123...", description="Token de API do provedor configurado no agente"),
     *             @OA\Property(property="prompt", type="string", example="Revise este codigo Python e sugira melhorias", minLength=10, description="Texto do prompt do usuario"),
     *             @OA\Property(property="file_content", type="string", nullable=true, example="def soma(a, b):\n    return a+b", description="Conteudo de arquivo ou dados adicionais"),
     *             @OA\Property(property="additional_data", type="string", nullable=true, example="O codigo deve seguir PEP 8", description="Informacoes extras para contexto")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resposta do agente recebida com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="response", type="string", example="Aqui estao as sugestoes de melhoria para seu codigo..."),
     *             @OA\Property(property="agent", type="string", example="Assistente de Codigo Python")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Agente nao encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validacao"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao processar prompt"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Nao autenticado"
     *     )
     * )
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
