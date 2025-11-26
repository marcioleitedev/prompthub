# 📚 Documentação Swagger API - PromptHub

## 🎯 Visão Geral

A API do PromptHub está completamente documentada usando **Swagger/OpenAPI 3.0**. A documentação interativa permite visualizar todos os endpoints, testar requisições e entender os schemas de dados.

## 🔗 Acessar a Documentação

### Produção
**🌐 https://api-prompthub.marcioleite.cloud/api/documentation**

### Desenvolvimento Local
**🏠 http://localhost:8000/api/documentation**

## ✨ Funcionalidades

### 📋 Endpoints Documentados

#### 🔐 Authentication
- `POST /api/register` - Registrar novo usuário
- `POST /api/login` - Login com email/senha
- `POST /api/logout` - Logout (requer autenticação)
- `GET /api/me` - Obter usuário autenticado
- `GET /api/auth/google` - Iniciar OAuth Google
- `GET /api/auth/google/callback` - Callback OAuth Google

#### 🤖 AI Prompts
- `POST /api/ai/prompt` - Enviar prompt para IA (OpenAI ou Gemini)

#### 👾 AI Agents
- `GET /api/agents` - Listar agentes do usuário
- `POST /api/agents` - Criar novo agente
- `GET /api/agents/{id}` - Obter agente específico
- `PUT /api/agents/{id}` - Atualizar agente
- `DELETE /api/agents/{id}` - Deletar agente
- `POST /api/agents/{id}/prompt` - Enviar prompt para agente específico

## 🔑 Como Usar

### 1. Acessar a Interface Swagger

Abra no navegador: `https://api-prompthub.marcioleite.cloud/api/documentation`

### 2. Registrar ou Fazer Login

#### Opção A: Registrar Novo Usuário
1. Procure o endpoint `POST /api/register`
2. Clique em **"Try it out"**
3. Preencha o JSON de exemplo:
```json
{
  "name": "Seu Nome",
  "email": "seu@email.com",
  "password": "senha123",
  "password_confirmation": "senha123"
}
```
4. Clique em **"Execute"**
5. Copie o `token` retornado na resposta

#### Opção B: Login com Usuário Existente
1. Procure o endpoint `POST /api/login`
2. Clique em **"Try it out"**
3. Preencha as credenciais:
```json
{
  "email": "seu@email.com",
  "password": "senha123"
}
```
4. Clique em **"Execute"**
5. Copie o `token` retornado

### 3. Autorizar com Token JWT

1. Clique no botão **"Authorize"** no topo da página (ícone de cadeado)
2. No campo `bearerAuth`, digite: `Bearer SEU_TOKEN_AQUI`
   - Exemplo: `Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...`
3. Clique em **"Authorize"**
4. Clique em **"Close"**

Agora você pode testar todos os endpoints protegidos! 🎉

### 4. Testar Endpoints Protegidos

#### Exemplo: Criar um Agente de IA

1. Encontre `POST /api/agents`
2. Clique em **"Try it out"**
3. Preencha o JSON:
```json
{
  "name": "Assistente Python",
  "description": "Agente especializado em código Python",
  "ai_provider": "openai",
  "ai_model": "gpt-3.5-turbo",
  "system_prompt": "Você é um especialista em Python...",
  "instructions": "Sempre siga PEP 8",
  "configuration": {
    "temperature": 0.7,
    "max_tokens": 1500
  }
}
```
4. Clique em **"Execute"**
5. Veja a resposta com o agente criado

#### Exemplo: Enviar Prompt Direto para IA

1. Encontre `POST /api/ai/prompt`
2. Clique em **"Try it out"**
3. Preencha:
```json
{
  "provider": "openai",
  "api_token": "sk-proj-SEU_TOKEN_OPENAI",
  "prompt": "Explique o que é inteligência artificial em 50 palavras",
  "temperature": 0.7,
  "max_tokens": 100
}
```
4. Clique em **"Execute"**
5. Receba a resposta da IA

## 🛠️ Regenerar Documentação

### Localmente

```bash
cd backend
php artisan l5-swagger:generate
```

### No Docker

```bash
docker exec -it prompthub-backend php artisan l5-swagger:generate
```

### No Easypanel

A documentação é gerada automaticamente durante o deploy através do:
- `Dockerfile` - executa `l5-swagger:generate` no build
- `start.sh` - gera docs toda vez que o container inicia

## 📝 Schemas e Modelos

### User
```json
{
  "id": 1,
  "name": "João Silva",
  "email": "joao@exemplo.com",
  "google_id": null,
  "avatar": null,
  "created_at": "2025-11-25T10:00:00.000000Z",
  "updated_at": "2025-11-25T10:00:00.000000Z"
}
```

### AI Agent
```json
{
  "id": 1,
  "user_id": 1,
  "name": "Assistente Python",
  "description": "Especialista em código Python",
  "ai_provider": "openai",
  "ai_model": "gpt-3.5-turbo",
  "system_prompt": "Você é um especialista...",
  "instructions": "Siga PEP 8",
  "configuration": {
    "temperature": 0.7,
    "max_tokens": 1500
  },
  "is_active": true,
  "created_at": "2025-11-25T10:00:00.000000Z",
  "updated_at": "2025-11-25T10:00:00.000000Z"
}
```

## 🔒 Segurança

### JWT Token
- **Formato**: Bearer Token
- **Localização**: Header `Authorization: Bearer {token}`
- **Validade**: 60 minutos (configurável via `JWT_TTL`)
- **Refresh**: Não implementado (fazer novo login)

### Rate Limiting
- Endpoints públicos: 60 requisições/minuto
- Endpoints autenticados: 120 requisições/minuto

## 🐛 Troubleshooting

### Swagger UI não carrega

```bash
# Limpar cache
php artisan config:clear
php artisan cache:clear

# Regenerar docs
php artisan l5-swagger:generate

# Verificar permissões
chmod -R 775 storage/api-docs
```

### Token JWT expirado

**Erro**: `Token has expired`

**Solução**: Faça login novamente e obtenha um novo token.

### CORS Error ao testar

**Erro**: `CORS policy: No 'Access-Control-Allow-Origin' header`

**Solução**: Verifique se `FRONTEND_URL` está configurada corretamente no `.env`.

### Endpoint não aparece na documentação

1. Verifique se as anotações `@OA\...` estão corretas no controller
2. Regenere a documentação: `php artisan l5-swagger:generate`
3. Limpe o cache: `php artisan config:clear`

## 📦 Arquivos de Configuração

### `config/l5-swagger.php`
Configuração principal do L5-Swagger

### `storage/api-docs/api-docs.json`
Arquivo JSON gerado com toda a documentação

### Controllers com Anotações
- `app/Http/Controllers/Controller.php` - Info geral da API
- `app/Http/Controllers/AuthController.php` - Endpoints de autenticação
- `app/Http/Controllers/AiPromptController.php` - Endpoints de prompts
- `app/Http/Controllers/AiAgentController.php` - Endpoints de agentes

## 📞 Suporte

Se encontrar problemas com a documentação:

1. Verifique os logs: `storage/logs/laravel.log`
2. Teste o health check: `/api/health`
3. Regenere a documentação
4. Entre em contato com o suporte

## 🚀 Melhorias Futuras

- [ ] Adicionar exemplos de requisições com `curl`
- [ ] Implementar refresh token automático
- [ ] Adicionar webhooks na documentação
- [ ] Criar collection do Postman
- [ ] Adicionar testes automatizados de API
- [ ] Documentar códigos de erro personalizados
- [ ] Adicionar rate limiting por endpoint

---

**Desenvolvido com ❤️ pela equipe PromptHub**
