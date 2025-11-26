<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessIaAgent;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    /**
     * @OA\Post(
     *     path="/tasks",
     *     summary="Criar nova tarefa de IA",
     *     description="Cria uma tarefa de processamento de IA em background. A tarefa e enviada para uma fila e processada de forma assincrona. Retorna imediatamente com status PENDING.",
     *     operationId="createTask",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados da tarefa",
     *         @OA\JsonContent(
     *             required={"prompt","ai_api_key"},
     *             @OA\Property(property="prompt", type="string", maxLength=2000, example="Analise este texto e extraia os principais topicos...", description="Prompt para o agente de IA (maximo 2000 caracteres)"),
     *             @OA\Property(property="ai_api_key", type="string", example="sk-proj-abc123...", description="Chave de API do provedor de IA (OpenAI, Gemini, etc)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Tarefa aceita e enviada para processamento",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tarefa enviada para o agente de IA."),
     *             @OA\Property(property="task_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
     *             @OA\Property(property="status", type="string", example="PENDING", enum={"PENDING", "PROCESSING", "COMPLETED", "FAILED"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validacao",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The prompt field is required."),
     *             @OA\Property(property="errors", type="object")
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
    public function store(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:2000',
            // O usuário precisa enviar a chave de IA na requisição
            'ai_api_key' => 'required|string', 
        ]);
        $user = Auth::user(); // Obtém o usuário autenticado via JWT
        $iaApiKey = $request->ai_api_key;
        
        // 1. Criar a tarefa com status PENDING
        $task = Task::create([
            'id' => Str::uuid(), 
            'user_id' => $user->id,
            'prompt' => $request->prompt,
            'status' => 'PENDING',
        ]);

        // 2. Despachar o Job para a fila, passando a Task e a chave da IA
        ProcessIaAgent::dispatch($task, $iaApiKey); 

        // 3. Retornar a resposta rápida (202 Accepted)
        return response()->json([
            'message' => 'Tarefa enviada para o agente de IA.',
            'task_id' => $task->id,
            'status' => $task->status,
        ], 202);
    }
    
    /**
     * @OA\Get(
     *     path="/tasks/{task}",
     *     summary="Obter status de uma tarefa",
     *     description="Retorna os detalhes e o status atual de uma tarefa de processamento de IA. O usuario so pode visualizar suas proprias tarefas.",
     *     operationId="getTask",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         required=true,
     *         description="UUID da tarefa",
     *         @OA\Schema(type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tarefa retornada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="prompt", type="string", example="Analise este texto..."),
     *             @OA\Property(property="status", type="string", example="COMPLETED", enum={"PENDING", "PROCESSING", "COMPLETED", "FAILED"}),
     *             @OA\Property(property="result", type="string", nullable=true, example="Analise completa: Os principais topicos sao...", description="Resultado do processamento quando status=COMPLETED"),
     *             @OA\Property(property="error", type="string", nullable=true, example="API timeout after 30s", description="Mensagem de erro quando status=FAILED"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-25T10:00:00.000000Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-25T10:00:30.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Usuario nao autorizado a ver esta tarefa",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Nao autorizado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tarefa nao encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Task]")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Nao autenticado"
     *     )
     * )
     */
    public function show(Task $task)
    {
        // Certifica que o usuário só pode ver suas próprias tarefas
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }
        return response()->json($task);
    }
}