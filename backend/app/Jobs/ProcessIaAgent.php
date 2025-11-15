<?php
namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessIaAgent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $task;
    public $iaApiKey; // Chave da IA enviada pelo usuário

    public function __construct(Task $task, string $iaApiKey)
    {
        $this->task = $task;
        $this->iaApiKey = $iaApiKey;
        // O Job será serializado e enviado para o Redis
    }

    public function handle(): void
    {
        $this->task->update(['status' => 'PROCESSING']);

        try {
            // 1. AUTENTICAR A CHAMADA EXTERNA USANDO $this->iaApiKey
            // Ex: $iaClient = new \OpenAI\Client(['api_key' => $this->iaApiKey]);
            
            // 2. Executar o Agente de IA com $this->task->prompt
            // $iaResponse = $iaClient->completions->create(...) 

            // SIMULAÇÃO: Processamento assíncrono e obtenção do resultado
            sleep(5); 
            $iaResult = [
                'completion' => 'A resposta do agente de IA para sua solicitação está pronta!',
                'tokens_used' => rand(200, 800)
            ];

            // 3. Atualizar a Task
            $this->task->update([
                'status' => 'COMPLETED',
                'result' => $iaResult
            ]);

            // Se você adicionar o Laravel Reverb (próximo passo), aqui você dispararia o evento:
            // event(new \App\Events\TaskCompleted($this->task));

        } catch (\Exception $e) {
            $this->task->update([
                'status' => 'FAILED',
                'result' => ['error' => 'Falha na IA: ' . $e->getMessage()]
            ]);
            throw $e; // Re-throw para o Laravel registrar a falha
        }
    }
}