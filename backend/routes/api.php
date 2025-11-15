<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\AiPromptController;
use App\Http\Controllers\AiAgentController;
use Illuminate\Support\Facades\Route;

// Health check endpoint (não requer autenticação)
Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'service' => 'PromptHub API'
    ]);
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Google OAuth routes
Route::get('auth/google', [AuthController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// Rotas protegidas (exigem o token JWT no header Authorization)
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    
    // AI Prompt routes
    Route::post('ai/prompt', [AiPromptController::class, 'sendPrompt']);
    
    // AI Agent routes
    Route::get('agents', [AiAgentController::class, 'index']);
    Route::post('agents', [AiAgentController::class, 'store']);
    Route::get('agents/{id}', [AiAgentController::class, 'show']);
    Route::put('agents/{id}', [AiAgentController::class, 'update']);
    Route::delete('agents/{id}', [AiAgentController::class, 'destroy']);
    Route::post('agents/{id}/prompt', [AiAgentController::class, 'sendPrompt']);
});
