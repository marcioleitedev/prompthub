<?php

// app/Events/TaskCompleted.php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel; // Importar PrivateChannel

class TaskCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task; // A tarefa com o resultado da IA

    /**
     * Cria uma nova instância de evento.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * O canal que o evento deve ser transmitido.
     */
    public function broadcastOn(): Channel
    {
        // Cria um canal privado nomeado 'user.{user_id}'.
        // Apenas o usuário autenticado com esse ID poderá ouvir.
        return new PrivateChannel('user.' . $this->task->user_id);
    }
    
    /**
     * Define um nome específico para o evento no frontend.
     */
    public function broadcastAs()
    {
        return 'task.completed';
    }
    
    /**
     * Dados a serem transmitidos para o frontend.
     * O objeto $this->task será convertido para array/JSON.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->task->id,
            'status' => $this->task->status,
            'result' => $this->task->result,
            'updated_at' => $this->task->updated_at
        ];
    }
}