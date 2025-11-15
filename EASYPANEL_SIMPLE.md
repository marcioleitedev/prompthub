# 🚀 Deploy Simplificado no Easypanel - PromptHub

## 📦 Arquitetura Otimizada

**Apenas 2 serviços Docker:**
- **Backend** (All-in-One): Laravel + Nginx + Queue Worker + Reverb WebSocket
- **Frontend**: Vue.js + Nginx

## 🎯 Vantagens

✅ Menos recursos no servidor  
✅ Mais fácil de gerenciar  
✅ Supervisord gerencia todos os processos  
✅ Apenas 2 serviços no Easypanel

## 📋 Pré-requisitos

- Conta no Easypanel
- MySQL e Redis externos (ou containers separados)
- Domínio configurado
- Credenciais Google OAuth

## 🔧 Configuração no Easypanel

### 1. Criar Projeto

No Easypanel, crie um novo projeto chamado `prompthub`.

### 2. Backend Service (All-in-One)

#### Criar serviço:
- **Name**: `backend`
- **Type**: Docker Image
- **Source**: GitHub Repository
- **Repository**: `marcioleitedev/prompthub`
- **Branch**: `main`
- **Dockerfile Path**: `backend/Dockerfile`
- **Build Context**: `backend`

#### Portas:
- **80** → Mapear para porta externa (ex: 8000)
- **8080** → Mapear para porta externa (ex: 8080) - WebSocket Reverb

#### Variáveis de Ambiente:

```env
# App
APP_NAME=PromptHub
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com
APP_KEY=base64:... (gerar: php artisan key:generate --show)

# Frontend
FRONTEND_URL=https://seu-dominio.com

# Database (MySQL externo)
DB_CONNECTION=mysql
DB_HOST=seu_ip_mysql
DB_PORT=3306
DB_DATABASE=portfolio
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

# Redis (externo)
REDIS_HOST=seu_ip_redis
REDIS_PASSWORD=sua_senha_redis
REDIS_PORT=6379
QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis

# Google OAuth
GOOGLE_CLIENT_ID=sua_client_id
GOOGLE_CLIENT_SECRET=sua_client_secret
GOOGLE_REDIRECT_URI=https://seu-dominio.com/api/auth/google/callback

# JWT
JWT_SECRET=seu_jwt_secret_aqui

# Reverb
REVERB_APP_ID=prompthub
REVERB_APP_KEY=key_prompthub
REVERB_APP_SECRET=secret_prompthub_xyz123
REVERB_HOST=0.0.0.0
REVERB_PORT=8080
REVERB_SCHEME=https
```

### 3. Frontend Service

#### Criar serviço:
- **Name**: `frontend`
- **Type**: Docker Image
- **Source**: GitHub Repository
- **Repository**: `marcioleitedev/prompthub`
- **Branch**: `main`
- **Dockerfile Path**: `frontend/Dockerfile`
- **Build Context**: `frontend`

#### Portas:
- **80** → Mapear para porta pública (80 ou 443)

#### Variáveis de Ambiente:

```env
VITE_API_URL=https://backend.seu-dominio.com
```

### 4. Configurar Domínios

#### Backend:
- Domínio: `api.seu-dominio.com` → porta 80 do container backend
- WebSocket: `ws.seu-dominio.com` → porta 8080 do container backend

#### Frontend:
- Domínio: `seu-dominio.com` → porta 80 do container frontend

## 🗄️ Banco de Dados

### Primeira vez - Rodar migrations:

```bash
# No Easypanel, abra o terminal do container backend
php artisan migrate --force
```

### Criar usuário admin (opcional):

```bash
php artisan tinker
User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('senha123')]);
```

## 🔍 Verificar Status

### Verificar processos no backend:

```bash
supervisorctl status
```

Deve mostrar:
- ✅ nginx
- ✅ php-fpm
- ✅ laravel-queue
- ✅ laravel-reverb

### Ver logs:

```bash
# Nginx
tail -f /var/log/nginx/error.log

# Supervisor
tail -f /var/log/supervisor/supervisord.log

# Laravel
tail -f /var/www/html/storage/logs/laravel.log
```

## 📊 Monitoramento

### Health Check Endpoints:

- Backend: `https://api.seu-dominio.com/api/health`
- Frontend: `https://seu-dominio.com`

## 🐛 Troubleshooting

### Backend não inicia:

```bash
# Verificar supervisor
supervisorctl status

# Reiniciar processos
supervisorctl restart all

# Ver logs
supervisorctl tail laravel-queue
supervisorctl tail laravel-reverb
```

### Queue não processa jobs:

```bash
# Verificar Redis
redis-cli -h SEU_IP -a SUA_SENHA ping

# Verificar queue worker
supervisorctl status laravel-queue

# Restart
supervisorctl restart laravel-queue
```

### WebSocket não conecta:

```bash
# Verificar Reverb
supervisorctl status laravel-reverb

# Testar porta
telnet ws.seu-dominio.com 8080

# Ver logs
supervisorctl tail laravel-reverb
```

## 🔄 Atualizações

No Easypanel, basta fazer push para o GitHub que o deploy automático será acionado.

Ou force rebuild:
1. Ir no serviço
2. Clicar em "Rebuild"

## 💾 Backup

### Backup do banco:

```bash
mysqldump -h SEU_IP -u USUARIO -p DATABASE > backup.sql
```

### Backup de arquivos:

```bash
# Dentro do container
tar -czf storage-backup.tar.gz storage/
```

## ✅ Checklist de Deploy

- [ ] MySQL acessível externamente
- [ ] Redis acessível externamente
- [ ] Variáveis de ambiente configuradas
- [ ] Domínios apontados corretamente
- [ ] SSL/TLS configurado
- [ ] Google OAuth configurado com URLs corretas
- [ ] Migrations rodadas
- [ ] Health check respondendo
- [ ] WebSocket conectando
- [ ] Queue processando jobs

## 🎉 Pronto!

Agora você tem apenas **2 serviços** no Easypanel:
- `backend` (porta 80 e 8080)
- `frontend` (porta 80)

Tudo gerenciado pelo Supervisor dentro do container backend! 🚀
