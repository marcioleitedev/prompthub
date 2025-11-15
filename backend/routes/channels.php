<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Aqui você registra as regras para autorizar usuários a escutar canais privados.
|
*/

// Regra de autorização para o nosso canal privado: 'user.{userId}'
// Apenas o usuário autenticado ($user) pode escutar se o ID do canal (userId)
// for igual ao seu próprio ID.
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});