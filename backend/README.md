# PromptHub API - Backend

## 🚀 Sobre o Projeto

PromptHub é uma API completa para gerenciamento de prompts de IA e agentes AI personalizados. Sistema que permite:

- ✅ Autenticação via email/senha ou Google OAuth
- ✅ Cadastro e gerenciamento de agentes de IA personalizados
- ✅ Envio de prompts para OpenAI e Google Gemini
- ✅ Documentação completa com Swagger/OpenAPI

## 📚 Documentação da API

### 🔗 Acessar Swagger UI

**Local:** http://localhost:8000/api/documentation  
**Produção:** https://api-prompthub.marcioleite.cloud/api/documentation

### 📖 Guias de Documentação

- **[SWAGGER_DOCUMENTATION.md](SWAGGER_DOCUMENTATION.md)** - Documentação completa da API
- **[QUICK_START_SWAGGER.md](QUICK_START_SWAGGER.md)** - Guia rápido para testar

## 🛠️ Tecnologias

- **Laravel 12.x** - Framework PHP
- **JWT Auth** - Autenticação via tokens
- **L5-Swagger** - Documentação OpenAPI/Swagger
- **Laravel Socialite** - OAuth Google
- **MySQL/PostgreSQL** - Banco de dados

## ⚙️ Instalação

```bash
# Clone o repositório
git clone [repo-url]
cd backend

# Instalar dependências
composer install

# Configurar ambiente
cp .env.example .env
php artisan key:generate
php artisan jwt:secret

# Configurar banco de dados no .env
# DB_CONNECTION=mysql
# DB_DATABASE=prompthub
# DB_USERNAME=root
# DB_PASSWORD=

# Executar migrations
php artisan migrate

# Gerar documentação Swagger
php artisan l5-swagger:generate

# Iniciar servidor
php artisan serve
```

## 🔐 Endpoints Principais

### Authentication
- `POST /api/register` - Registrar usuário
- `POST /api/login` - Login
- `POST /api/logout` - Logout
- `GET /api/me` - Dados do usuário autenticado
- `GET /api/auth/google` - OAuth Google
- `GET /api/auth/google/callback` - Callback OAuth

### AI Prompts
- `POST /api/ai/prompt` - Enviar prompt para IA

### AI Agents (CRUD Completo)
- `GET /api/agents` - Listar agentes
- `POST /api/agents` - Criar agente
- `GET /api/agents/{id}` - Ver agente
- `PUT /api/agents/{id}` - Atualizar agente
- `DELETE /api/agents/{id}` - Deletar agente
- `POST /api/agents/{id}/prompt` - Enviar prompt para agente

### Health Check
- `GET /api/health` - Status da API

## 🧪 Testando a API

### Via Swagger UI (Recomendado)
1. Acesse http://localhost:8000/api/documentation
2. Faça login/registro para obter token JWT
3. Clique em "Authorize" e cole o token
4. Teste qualquer endpoint diretamente na interface

### Via cURL

```bash
# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Listar agentes (com token)
curl -X GET http://localhost:8000/api/agents \
  -H "Authorization: Bearer SEU_TOKEN_JWT"
```

## 📦 Comandos Úteis

```bash
# Regenerar documentação Swagger
php artisan l5-swagger:generate

# Limpar cache
php artisan cache:clear
php artisan config:clear

# Ver rotas
php artisan route:list

# Executar testes
php artisan test
```

## 🌐 Deploy em Produção

### Docker

```bash
# Build da imagem
docker build -t prompthub-api .

# Executar container
docker run -p 8000:8000 prompthub-api
```

### Easypanel / Servidores

1. Configure variáveis de ambiente (`.env`)
2. Execute migrations: `php artisan migrate --force`
3. Gere documentação: `php artisan l5-swagger:generate`
4. Configure Nginx/Apache para servir `public/index.php`

## 🔑 Variáveis de Ambiente Importantes

```env
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5173

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prompthub
DB_USERNAME=root
DB_PASSWORD=

JWT_SECRET=seu-jwt-secret-aqui
JWT_TTL=60

GOOGLE_CLIENT_ID=seu-google-client-id
GOOGLE_CLIENT_SECRET=seu-google-client-secret
GOOGLE_REDIRECT=http://localhost:8000/api/auth/google/callback
```

## 📝 Licença

MIT License - PromptHub 2025

---

**Desenvolvido com ❤️ usando Laravel + Swagger**
