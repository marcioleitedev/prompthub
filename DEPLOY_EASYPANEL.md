# 🚀 Guia de Deploy no Easypanel

## 📋 Pré-requisitos

1. Conta no Easypanel
2. Docker Hub account
3. MySQL e Redis configurados no Easypanel
4. Domínios configurados:
   - `api-prompthub.marcioleite.cloud` → Backend API
   - `marcioleite.cloud` → Frontend

---

## 🔧 Passo 1: Configurar Variáveis de Ambiente

No Easypanel, configure as seguintes variáveis de ambiente para o serviço do backend:

```env
APP_NAME=PromptHub API
APP_ENV=production
APP_KEY=base64:SEU_APP_KEY_AQUI
APP_DEBUG=false
APP_URL=https://api-prompthub.marcioleite.cloud
FRONTEND_URL=https://marcioleite.cloud

DB_CONNECTION=mysql
DB_HOST=SEU_HOST_MYSQL
DB_PORT=3306
DB_DATABASE=prompthub
DB_USERNAME=SEU_USUARIO
DB_PASSWORD=SUA_SENHA

REDIS_HOST=SEU_HOST_REDIS
REDIS_PASSWORD=SUA_SENHA_REDIS
REDIS_PORT=6379

GOOGLE_CLIENT_ID=SEU_GOOGLE_CLIENT_ID
GOOGLE_CLIENT_SECRET=SEU_GOOGLE_CLIENT_SECRET
GOOGLE_REDIRECT_URI=https://api-prompthub.marcioleite.cloud/api/auth/google/callback

JWT_SECRET=SEU_JWT_SECRET

SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis
BROADCAST_CONNECTION=reverb

L5_SWAGGER_USE_ABSOLUTE_PATH=true
L5_FORMAT_TO_USE_FOR_DOCS=json
```

---

## 🏗️ Passo 2: Build e Push das Imagens Docker

### Backend API

```bash
cd backend

# Build da imagem
docker build -t marcioleitedev/prompthub-backend:latest .

# Push para Docker Hub
docker push marcioleitedev/prompthub-backend:latest
```

### Frontend

```bash
cd frontend

# Build da imagem
docker build -t marcioleitedev/prompthub-frontend:latest .

# Push para Docker Hub
docker push marcioleitedev/prompthub-frontend:latest
```

---

## 📦 Passo 3: Configurar Serviços no Easypanel

### Serviço 1: Backend API

1. **Criar novo serviço** no Easypanel
2. **Nome**: `prompthub-backend`
3. **Tipo**: Docker
4. **Imagem**: `marcioleitedev/prompthub-backend:latest`
5. **Porta**: 80
6. **Domínio**: `api-prompthub.marcioleite.cloud`
7. **Variáveis de ambiente**: Adicionar todas as variáveis acima
8. **Health check**: `/api/health`

### Serviço 2: Frontend

1. **Criar novo serviço** no Easypanel
2. **Nome**: `prompthub-frontend`
3. **Tipo**: Docker
4. **Imagem**: `marcioleitedev/prompthub-frontend:latest`
5. **Porta**: 80
6. **Domínio**: `marcioleite.cloud`
7. **Variáveis de ambiente**:
   ```env
   VITE_API_URL=https://api-prompthub.marcioleite.cloud
   ```

---

## 🗄️ Passo 4: Configurar Banco de Dados

### Rodar Migrations

Conecte ao container do backend e execute:

```bash
php artisan migrate --force
```

Ou configure a variável de ambiente `DB_AUTO_MIGRATE=true` para rodar automaticamente.

---

## 📚 Passo 5: Acessar a Documentação Swagger

Após o deploy, a documentação estará disponível em:

**🔗 https://api-prompthub.marcioleite.cloud/api/documentation**

### Funcionalidades da Documentação:

1. **Visualizar todos os endpoints** da API
2. **Testar cada endpoint** diretamente na interface
3. **Ver exemplos de request/response**
4. **Autenticar com JWT** usando o botão "Authorize"

---

## 🔐 Passo 6: Testar Autenticação

### 1. Registrar usuário
```bash
POST /api/register
{
  "name": "Teste User",
  "email": "teste@exemplo.com",
  "password": "senha123",
  "password_confirmation": "senha123"
}
```

### 2. Copiar o token retornado

### 3. Clicar em "Authorize" no Swagger UI

### 4. Inserir: `Bearer SEU_TOKEN_AQUI`

### 5. Testar endpoints protegidos

---

## 🔄 Comandos Úteis de Manutenção

### Limpar Cache
```bash
docker exec -it prompthub-backend php artisan cache:clear
docker exec -it prompthub-backend php artisan config:clear
docker exec -it prompthub-backend php artisan route:clear
```

### Regenerar Documentação Swagger
```bash
docker exec -it prompthub-backend php artisan l5-swagger:generate
```

### Ver Logs
```bash
docker logs -f prompthub-backend
docker logs -f prompthub-frontend
```

### Rodar Queue Worker
```bash
docker exec -it prompthub-backend php artisan queue:work --tries=3
```

---

## 🎯 Checklist de Deploy

- [ ] MySQL configurado e acessível
- [ ] Redis configurado e acessível
- [ ] Variáveis de ambiente configuradas
- [ ] APP_KEY gerado (`php artisan key:generate`)
- [ ] JWT_SECRET gerado (`php artisan jwt:secret`)
- [ ] Imagens Docker buildadas e pusheadas
- [ ] Serviços criados no Easypanel
- [ ] Domínios apontando corretamente
- [ ] Migrations rodadas
- [ ] SSL/HTTPS configurado
- [ ] Swagger acessível em `/api/documentation`
- [ ] Health check funcionando em `/api/health`
- [ ] Frontend acessível
- [ ] Autenticação funcionando (registro/login)
- [ ] Google OAuth configurado (opcional)

---

## 🆘 Troubleshooting

### Erro: "Class not found"
```bash
docker exec -it prompthub-backend composer dump-autoload
```

### Erro: "Permission denied" em storage
```bash
docker exec -it prompthub-backend chmod -R 775 storage bootstrap/cache
docker exec -it prompthub-backend chown -R www-data:www-data storage bootstrap/cache
```

### Swagger não carrega
```bash
docker exec -it prompthub-backend php artisan l5-swagger:generate
docker exec -it prompthub-backend php artisan config:clear
```

### CORS errors no frontend
Verifique se `FRONTEND_URL` está correta no `.env` do backend

---

## 📞 Suporte

Para problemas ou dúvidas:
- Verifique os logs: `docker logs prompthub-backend`
- Teste o health check: `https://api-prompthub.marcioleite.cloud/api/health`
- Acesse a documentação: `https://api-prompthub.marcioleite.cloud/api/documentation`
