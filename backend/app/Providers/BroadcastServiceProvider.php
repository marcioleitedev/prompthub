<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Define que o Laravel deve carregar o arquivo de rotas de canais.
        Broadcast::routes(['middleware' => ['auth:api']]); 
        
        // 2. Carrega as rotas (regras de autorização) de canais do arquivo routes/channels.php
        require base_path('routes/channels.php');
    }
}