# 📚 Documentação API Swagger - PromptHub

## 🎯 Visão Geral

A API do PromptHub está completamente documentada usando **Swagger/OpenAPI 3.0**. A documentação interativa permite visualizar, testar e explorar todos os endpoints da API diretamente pelo navegador.

## 🔗 Acesso à Documentação

### Ambiente Local
```
http://localhost:8000/api/documentation
```

### Ambiente de Produção
```
https://api-prompthub.marcioleite.cloud/api/documentation
```

## ✨ Recursos Disponíveis

### 📋 Grupos de Endpoints Documentados

#### 1. **Health** 
- `GET /health` - Health check da API (público)

#### 2. **Authentication** 
- `POST /register` - Registro de novo usuário
- `POST /login` - Login com email/senha
- `POST /logout` - Logout (requer autenticação)
- `GET /me` - Obter dados do usuário autenticado
- `GET /auth/google` - Iniciar OAuth Google
- `GET /auth/google/callback` - Callback OAuth Google

#### 3. **AI Prompts**
- `POST /ai/prompt` - Enviar prompt para OpenAI ou Gemini (requer autenticação)

#### 4. **AI Agents**
- `GET /agents` - Listar agentes do usuário
- `POST /agents` - Criar novo agente
- `GET /agents/{id}` - Obter agente específico
- `PUT /agents/{id}` - Atualizar agente
- `DELETE /agents/{id}` - Deletar agente
- `POST /agents/{id}/prompt` - Enviar prompt para agente específico

## 🔐 Como Testar Endpoints Autenticados

### Passo 1: Registrar ou Fazer Login

1. Acesse a documentação Swagger
2. Navegue até **Authentication → POST /register** ou **POST /login**
3. Clique em **"Try it out"**
4. Preencha o JSON de exemplo:

**Registro:**
```json
{
  "name": "João Silva",
  "email": "joao@exemplo.com",
  "password": "senha123",
  "password_confirmation": "senha123"
}
```

**Login:**
```json
{
  "email": "joao@exemplo.com",
  "password": "senha123"
}
```

5. Clique em **"Execute"**
6. **Copie o token JWT** retornado na resposta

### Passo 2: Autorizar no Swagger

1. No topo da página Swagger, clique no botão **"Authorize"** 🔓
2. Cole o token JWT no campo (apenas o token, sem "Bearer")
3. Clique em **"Authorize"**
4. Clique em **"Close"**

Agora todos os endpoints protegidos podem ser testados! 🎉

### Passo 3: Testar Endpoints Autenticados

Exemplo - **Criar Agente de IA:**

1. Navegue até **AI Agents → POST /agents**
2. Clique em **"Try it out"**
3. Use este JSON de exemplo:

```json
{
  "name": "Assistente de Código Python",
  "description": "Agente especializado em revisar e otimizar código Python",
  "ai_provider": "openai",
  "ai_model": "gpt-4",
  "system_prompt": "Você é um especialista em Python com 10 anos de experiência. Sempre siga as melhores práticas PEP 8.",
  "instructions": "Revise o código fornecido, identifique problemas e sugira melhorias específicas.",
  "configuration": {
    "temperature": 0.5,
    "max_tokens": 2000
  }
}
```

4. Clique em **"Execute"**

## 📝 Exemplos de Uso

### Enviar Prompt para OpenAI

**Endpoint:** `POST /ai/prompt`

```json
{
  "provider": "openai",
  "api_token": "sk-proj-...",
  "prompt": "Explique o que é inteligência artificial em 100 palavras",
  "temperature": 0.7,
  "max_tokens": 150
}
```

### Enviar Prompt para Google Gemini

```json
{
  "provider": "gemini",
  "api_token": "AIza...",
  "prompt": "Crie um exemplo de código Python para sorting algorithm",
  "temperature": 0.5,
  "max_tokens": 500
}
```

### Enviar Prompt para Agente Específico

**Endpoint:** `POST /agents/{id}/prompt`

```json
{
  "api_token": "sk-proj-...",
  "prompt": "Revise este código e sugira melhorias",
  "file_content": "def soma(a, b):\n    return a+b",
  "additional_data": "O código deve seguir PEP 8 e incluir type hints"
}
```

## 🛠️ Regenerar Documentação

Sempre que modificar os controllers com novas anotações OpenAPI, execute:

```bash
php artisan l5-swagger:generate
```

## 📦 Estrutura das Anotações

As anotações OpenAPI estão localizadas em:

- **`app/Http/Controllers/Controller.php`** - Configuração geral da API
- **`app/Http/Controllers/AuthController.php`** - Endpoints de autenticação
- **`app/Http/Controllers/AiPromptController.php`** - Endpoints de prompts diretos
- **`app/Http/Controllers/AiAgentController.php`** - Endpoints de agentes de IA
- **`routes/api.php`** - Health check endpoint

## 🔑 Esquema de Segurança

A API utiliza **JWT Bearer Token** para autenticação:

```
Authorization: Bearer {seu-token-jwt-aqui}
```

O Swagger está configurado para aceitar tokens via botão "Authorize".

## 🌐 Servidores Configurados

- **Local:** `http://localhost:8000/api`
- **Produção:** `https://api-prompthub.marcioleite.cloud/api`

Você pode alternar entre servidores diretamente na interface do Swagger.

## 📄 Licença

MIT License - PromptHub 2025

## 🤝 Suporte

Para dúvidas ou problemas com a API:
- Email: marciobleite1977@gmail.com
- GitHub Issues: [Reportar problema]

---

**Desenvolvido com ❤️ usando Laravel + L5-Swagger**
