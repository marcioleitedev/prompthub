<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessIaAgent;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TaskController extends Controller
{
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
    
    public function show(Task $task)
    {
        // Certifica que o usuário só pode ver suas próprias tarefas
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }
        return response()->json($task);
    }
}