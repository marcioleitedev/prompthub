# Configuração de Autenticação com Google

## Configuração do Backend (Laravel)

### 1. Instalar Dependências
```bash
cd backend
composer install
```

### 2. Configurar Variáveis de Ambiente
Copie o arquivo `.env.example` para `.env` e configure:

```env
# Google OAuth
GOOGLE_CLIENT_ID=seu_client_id_aqui
GOOGLE_CLIENT_SECRET=seu_client_secret_aqui
GOOGLE_REDIRECT_URI=http://localhost:8000/api/auth/google/callback

# Frontend URL
FRONTEND_URL=http://localhost:5173
```

### 3. Obter Credenciais do Google
1. Acesse o [Google Cloud Console](https://console.cloud.google.com/)
2. Crie um novo projeto ou selecione um existente
3. Vá em "APIs & Services" > "Credentials"
4. Clique em "Create Credentials" > "OAuth Client ID"
5. Configure:
   - Application type: Web application
   - Authorized redirect URIs: `http://localhost:8000/api/auth/google/callback`
6. Copie o Client ID e Client Secret para o arquivo `.env`

### 4. Executar Migrations
```bash
php artisan migrate
```

### 5. Iniciar o Servidor
```bash
php artisan serve
```

O backend estará rodando em `http://localhost:8000`

## Configuração do Frontend (Vue.js)

### 1. Instalar Dependências
```bash
cd frontend
npm install
```

### 2. Iniciar o Servidor de Desenvolvimento
```bash
npm run dev
```

O frontend estará rodando em `http://localhost:5173`

## Estrutura Criada

### Backend
- ✅ Migration para adicionar campos `google_id` e `avatar` na tabela `users`
- ✅ Controller `AuthController` com métodos para:
  - Login tradicional
  - Cadastro tradicional
  - Login com Google
  - Callback do Google
  - Logout
  - Obter usuário autenticado
- ✅ Rotas API configuradas em `routes/api.php`
- ✅ Configuração do Socialite em `config/services.php`

### Frontend
- ✅ Vue Router configurado
- ✅ Página de Login/Cadastro (`src/views/LoginView.vue`)
- ✅ Página de Home (`src/views/HomeView.vue`)
- ✅ Página de Callback do Google (`src/views/AuthCallback.vue`)
- ✅ Service de autenticação (`src/services/auth.js`)
- ✅ Rotas protegidas com guard de autenticação

## Funcionalidades

### Login/Cadastro Tradicional
- Cadastro com nome, email e senha
- Login com email e senha
- Validação de formulários
- Armazenamento de token JWT no localStorage

### Login com Google
- Botão "Continuar com Google" nas páginas de login e cadastro
- Redirecionamento para autenticação do Google
- Callback automático após autenticação
- Criação/atualização de usuário no banco de dados
- Geração de token JWT

### Segurança
- Rotas protegidas com JWT
- Middleware de autenticação
- Token armazenado no localStorage
- Interceptor Axios para adicionar token nas requisições

## URLs Importantes

- Backend: `http://localhost:8000`
- Frontend: `http://localhost:5173`
- API Login: `http://localhost:8000/api/login`
- API Registro: `http://localhost:8000/api/register`
- Google OAuth: `http://localhost:8000/api/auth/google`
- Google Callback: `http://localhost:8000/api/auth/google/callback`

## Testando

1. Acesse `http://localhost:5173`
2. Você será redirecionado para `/login`
3. Faça cadastro tradicional ou clique em "Continuar com Google"
4. Após autenticação, você será redirecionado para a home
5. O token JWT será armazenado e usado nas próximas requisições

## Observações

- Certifique-se de que o backend está rodando antes de iniciar o frontend
- As URLs do backend estão hardcoded no frontend. Para produção, use variáveis de ambiente
- O password é opcional para usuários que se autenticam via Google
- O avatar do Google é salvo no banco de dados
