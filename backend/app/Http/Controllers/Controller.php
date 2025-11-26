<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="PromptHub API Documentation",
 *     description="API completa para gerenciamento de prompts de IA e agentes AI. Sistema que permite autenticacao via email/senha ou Google OAuth, cadastro e gerenciamento de agentes de IA personalizados, envio de prompts para OpenAI e Google Gemini.",
 *     @OA\Contact(
 *         email="suporte@prompthub.com",
 *         name="PromptHub Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Local Development Server"
 * )
 * 
 * @OA\Server(
 *     url="https://dflix-prompthub-backend.a8zera.easypanel.host/api",
 *     description="Production Server (Easypanel)"
 * )
 * 
 * @OA\Server(
 *     url="https://api-prompthub.marcioleite.cloud/api",
 *     description="Production Server (Alternative)"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="JWT Authorization header using the Bearer scheme. Example: 'Authorization: Bearer {token}'"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints para autenticacao de usuarios (registro, login, logout, OAuth Google)"
 * )
 * 
 * @OA\Tag(
 *     name="Health",
 *     description="Endpoint de health check para monitoramento da API"
 * )
 * 
 * @OA\Tag(
 *     name="AI Prompts",
 *     description="Endpoints para enviar prompts diretamente para provedores de IA (OpenAI, Gemini)"
 * )
 * 
 * @OA\Tag(
 *     name="AI Agents",
 *     description="Endpoints para gerenciar agentes de IA personalizados (CRUD completo e envio de prompts)"
 * )
 * 
 * @OA\Tag(
 *     name="Tasks",
 *     description="Endpoints para gerenciar tarefas de processamento de IA em background (criacao e consulta de status)"
 * )
 */
abstract class Controller
{
    //
}
