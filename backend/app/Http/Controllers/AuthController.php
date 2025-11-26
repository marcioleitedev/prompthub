<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Registrar novo usuario",
     *     description="Cria uma nova conta de usuario com email e senha. Retorna o token JWT apos cadastro bem-sucedido.",
     *     operationId="register",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Dados do usuario para registro",
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Joao Silva", description="Nome completo do usuario"),
     *             @OA\Property(property="email", type="string", format="email", example="joao@exemplo.com", description="Email unico do usuario"),
     *             @OA\Property(property="password", type="string", format="password", example="senha123", minLength=6, description="Senha do usuario (minimo 6 caracteres)"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="senha123", description="Confirmacao da senha")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuario registrado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User successfully registered"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Joao Silva"),
     *                 @OA\Property(property="email", type="string", example="joao@exemplo.com"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-25T10:00:00.000000Z")
     *             ),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validacao",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email has already been taken."))
     *             )
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Login de usuario",
     *     description="Autentica usuario com email e senha. Retorna token JWT para autorizacao.",
     *     operationId="login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Credenciais do usuario",
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="joao@exemplo.com"),
     *             @OA\Property(property="password", type="string", format="password", example="senha123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login realizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Joao Silva"),
     *                 @OA\Property(property="email", type="string", example="joao@exemplo.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciais invalidas",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid credentials")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = JWTAuth::user();

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     summary="Logout de usuario",
     *     description="Invalida o token JWT atual. Requer autenticacao.",
     *     operationId="logout",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout realizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully logged out")
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
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * @OA\Get(
     *     path="/me",
     *     summary="Obter usuario autenticado",
     *     description="Retorna as informacoes do usuario atualmente autenticado.",
     *     operationId="getAuthenticatedUser",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dados do usuario autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Joao Silva"),
     *             @OA\Property(property="email", type="string", example="joao@exemplo.com"),
     *             @OA\Property(property="google_id", type="string", nullable=true, example="1234567890"),
     *             @OA\Property(property="avatar", type="string", nullable=true, example="https://lh3.googleusercontent.com/..."),
     *             @OA\Property(property="created_at", type="string", format="date-time")
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
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * @OA\Get(
     *     path="/auth/google",
     *     summary="Iniciar autenticacao Google OAuth",
     *     description="Retorna a URL de redirecionamento para autenticacao via Google OAuth",
     *     operationId="redirectToGoogle",
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="URL de redirecionamento do Google",
     *         @OA\JsonContent(
     *             @OA\Property(property="url", type="string", example="https://accounts.google.com/o/oauth2/v2/auth?client_id=...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao gerar URL do Google",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Erro ao redirecionar para Google")
     *         )
     *     )
     * )
     */
    public function redirectToGoogle()
    {
        try {
            // Gera state manualmente para evitar uso de sessão
            $state = Str::random(40);
            
            $query = http_build_query([
                'client_id' => config('services.google.client_id'),
                'redirect_uri' => config('services.google.redirect'),
                'scope' => 'openid profile email',
                'response_type' => 'code',
                'state' => $state,
            ]);
            
            $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . $query;
            
            return response()->json(['url' => $url]);
        } catch (\Exception $e) {
            Log::error('Google redirect error: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao redirecionar para Google'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/auth/google/callback",
     *     summary="Callback do Google OAuth",
     *     description="Endpoint de callback do Google OAuth. Processa o codigo de autorizacao e redireciona para o frontend com o token JWT.",
     *     operationId="handleGoogleCallback",
     *     tags={"Authentication"},
     *     @OA\Parameter(
     *         name="code",
     *         in="query",
     *         required=true,
     *         description="Codigo de autorizacao retornado pelo Google",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redireciona para o frontend com token ou erro",
     *         @OA\Header(
     *             header="Location",
     *             description="URL de redirecionamento (frontend/auth/callback?token={token} ou frontend/login?error=...)",
     *             @OA\Schema(type="string")
     *         )
     *     )
     * )
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $code = $request->input('code');
            
            if (!$code) {
                throw new \Exception('Código de autorização não fornecido');
            }
            
            // Troca o código pelo token de acesso
            $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect_uri' => config('services.google.redirect'),
                'grant_type' => 'authorization_code',
            ]);
            
            if ($tokenResponse->failed()) {
                throw new \Exception('Falha ao obter token: ' . $tokenResponse->body());
            }
            
            $tokenData = $tokenResponse->json();
            $accessToken = $tokenData['access_token'];
            
            // Obtém informações do usuário
            $userResponse = Http::withToken($accessToken)->get('https://www.googleapis.com/oauth2/v2/userinfo');
            
            if ($userResponse->failed()) {
                throw new \Exception('Falha ao obter dados do usuário');
            }
            
            $googleUser = $userResponse->json();

            $googleUser = $userResponse->json();

            // Find or create user
            $user = User::where('google_id', $googleUser['id'])
                ->orWhere('email', $googleUser['email'])
                ->first();

            if ($user) {
                // Update existing user
                $user->update([
                    'google_id' => $googleUser['id'],
                    'avatar' => $googleUser['picture'] ?? null,
                ]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser['name'],
                    'email' => $googleUser['email'],
                    'google_id' => $googleUser['id'],
                    'avatar' => $googleUser['picture'] ?? null,
                    'password' => Hash::make(Str::random(24)), // Random password for OAuth users
                ]);
            }

            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            // Redirect to frontend with token
            $frontendUrl = rtrim(env('FRONTEND_URL', 'https://marcioleite.cloud'), '/');
            return redirect()->away("{$frontendUrl}/auth/callback?token={$token}");

        } catch (\Exception $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            $frontendUrl = rtrim(env('FRONTEND_URL', 'https://marcioleite.cloud'), '/');
            return redirect()->away("{$frontendUrl}/login?error=authentication_failed&message=" . urlencode($e->getMessage()));
        }
    }
}
