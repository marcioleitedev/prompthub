# 🚀 Guia Rápido - Testando API no Swagger

## 📌 Acesso Rápido

**URL Local:** http://localhost:8000/api/documentation

## ⚡ Testando em 3 Passos

### 1️⃣ OBTER TOKEN

**Opção A - Registrar Novo Usuário:**
```
POST /register

{
  "name": "Seu Nome",
  "email": "seu@email.com",
  "password": "senha123",
  "password_confirmation": "senha123"
}
```

**Opção B - Login com Usuário Existente:**
```
POST /login

{
  "email": "seu@email.com",
  "password": "senha123"
}
```

✅ **Copie o `token` da resposta**

### 2️⃣ AUTORIZAR NO SWAGGER

1. Clique no botão verde **"Authorize"** 🔓 (canto superior direito)
2. Cole seu token JWT
3. Clique em **"Authorize"**
4. Feche o modal

### 3️⃣ TESTAR QUALQUER ENDPOINT

Agora você pode:
- ✅ Ver seus dados: `GET /me`
- ✅ Criar agente: `POST /agents`
- ✅ Listar agentes: `GET /agents`
- ✅ Enviar prompt: `POST /ai/prompt`

## 🤖 Exemplo Prático - Criar Agente Python

```json
POST /agents

{
  "name": "Revisor Python",
  "description": "Analisa e melhora código Python",
  "ai_provider": "openai",
  "system_prompt": "Você é especialista Python",
  "configuration": {
    "temperature": 0.5,
    "max_tokens": 1500
  }
}
```

## 💬 Exemplo - Enviar Prompt para OpenAI

```json
POST /ai/prompt

{
  "provider": "openai",
  "api_token": "sk-proj-SEU_TOKEN_AQUI",
  "prompt": "Explique o que é recursão em Python com exemplo",
  "temperature": 0.7,
  "max_tokens": 500
}
```

## 📋 Comandos Úteis

**Iniciar servidor local:**
```bash
php artisan serve
```

**Regenerar documentação após mudanças:**
```bash
php artisan l5-swagger:generate
```

**Ver todas as rotas:**
```bash
php artisan route:list
```

## 🎯 Dicas Importantes

1. **Token JWT expira** - Se receber erro 401, faça login novamente
2. **API Token** - Precisa ter seu próprio token OpenAI/Gemini para usar `/ai/prompt`
3. **Agentes** - Cada agente só pode ser acessado pelo usuário que o criou
4. **Health Check** - Use `/health` para testar se API está online (não precisa token)

## 🔗 Links Úteis

- 📖 Documentação Completa: `SWAGGER_DOCUMENTATION.md`
- 🌐 Swagger UI Local: http://localhost:8000/api/documentation
- 🚀 API de Produção: https://api-prompthub.marcioleite.cloud/api/documentation

---

**Pronto para testar! 🎉**
