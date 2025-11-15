# Configuração Easypanel - PromptHub

## 📋 Passos para Deploy no Easypanel

### 1. Criar novo serviço

1. Acesse seu Easypanel
2. Clique em **"+ Create"** → **"Service"**
3. Escolha **"From GitHub"**

### 2. Conectar repositório

1. Conecte sua conta do GitHub
2. Selecione o repositório `portfolio-IA`
3. Branch: `main`
4. Auto Deploy: **Ativado** ✅

### 3. Configurar Build

**Build Method:** Docker Compose

**Compose File:** `docker-compose.production.yml`

### 4. Configurar Variáveis de Ambiente

Adicione as seguintes variáveis no Easypanel:

```env
# Application
APP_NAME=PromptHub
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com
FRONTEND_URL=https://seu-dominio.com

# Database
DB_CONNECTION=mysql
DB_HOST=SEU_IP_MYSQL_AQUI
DB_PORT=3306
DB_DATABASE=seu_database
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

# Redis
REDIS_HOST=SEU_IP_REDIS_AQUI
REDIS_PASSWORD=sua_senha_redis
REDIS_PORT=6379
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
CACHE_DRIVER=redis

# Google OAuth
GOOGLE_CLIENT_ID=sua_google_client_id_aqui
GOOGLE_CLIENT_SECRET=sua_google_client_secret_aqui
GOOGLE_REDIRECT_URI=https://seu-dominio.com/api/auth/google/callback

# JWT & Keys (gerar após primeiro deploy)
APP_KEY=base64:...
JWT_SECRET=...

# Reverb
REVERB_APP_ID=portfolio_ia_app
REVERB_APP_KEY=key_portfolio_ia
REVERB_APP_SECRET=secret_portfolio_ia_xyz123
REVERB_HOST=0.0.0.0
REVERB_PORT=8080
REVERB_SCHEME=https
```

### 5. Configurar Portas

No Easypanel, configure as seguintes portas:

| Service | Port | Public |
|---------|------|--------|
| frontend | 80 | ✅ Sim |
| frontend | 443 | ✅ Sim (SSL) |
| backend | 8000 | ✅ Sim |
| reverb | 8080 | ✅ Sim |
| queue-worker | - | ❌ Não |

### 6. Configurar Domínio

1. Vá em **Domains** no Easypanel
2. Clique em **"Add Domain"**
3. Digite seu domínio (ex: `prompthub.seudominio.com`)
4. Easypanel configurará automaticamente:
   - ✅ Proxy reverso para os containers
   - ✅ SSL via Let's Encrypt
   - ✅ Renovação automática de certificados

### 7. Configurar Rotas (Routing)

Configure o proxy reverso no Easypanel:

```
/ → frontend:80
/api → backend:8000
/ws → reverb:8080
```

### 8. Primeiro Deploy

1. Clique em **"Deploy"**
2. Aguarde o build dos containers
3. Após deploy, execute os comandos:

```bash
# Gerar APP_KEY
docker exec prompthub-backend php artisan key:generate --show

# Gerar JWT_SECRET
docker exec prompthub-backend php artisan jwt:secret --show

# Rodar migrations
docker exec prompthub-backend php artisan migrate --force
```

4. Adicione `APP_KEY` e `JWT_SECRET` nas variáveis de ambiente
5. Faça um novo deploy

### 9. Configurar Auto Deploy

No Easypanel:
1. Vá em **Settings** → **GitHub Integration**
2. Ative **"Auto Deploy on Push"**
3. Branch: `main`

Agora, toda vez que você fizer push para `main`, o Easypanel fará deploy automático! 🚀

### 10. Verificar Deploy

Acesse:
- Frontend: `https://seu-dominio.com`
- Backend Health: `https://seu-dominio.com/api/health`
- Reverb: `wss://seu-dominio.com/ws`

## 🔄 Atualizações Automáticas

Com o setup acima, você tem 2 formas de deploy automático:

### Opção 1: GitHub Actions (Recomendado)
- Push para `main` → GitHub Actions → SSH no servidor → Deploy

### Opção 2: Easypanel Auto Deploy
- Push para `main` → Webhook do GitHub → Easypanel → Deploy

**💡 Dica:** Use ambos! GitHub Actions para validações/testes e Easypanel para deploy rápido.

## 🐛 Troubleshooting

### Container não inicia
```bash
# Ver logs no Easypanel
Logs → Selecione o container → Ver últimas 100 linhas
```

### Erro de permissão
```bash
docker exec prompthub-backend chmod -R 775 storage bootstrap/cache
docker exec prompthub-backend chown -R www-data:www-data storage bootstrap/cache
```

### Banco de dados não conecta
- Verifique se o IP do banco está acessível do servidor Easypanel
- Teste: `telnet SEU_IP_MYSQL_AQUI 3306`
- Verifique firewall do banco de dados

### SSL não funciona
- Certifique-se que o domínio está apontando para o servidor
- Aguarde alguns minutos para propagação DNS
- Easypanel renovará automaticamente os certificados

## 📊 Monitoramento

No Easypanel você pode monitorar:
- 📈 CPU e RAM de cada container
- 📊 Logs em tempo real
- 🔄 Status dos containers
- 📉 Métricas de rede

## 🎉 Pronto!

Sua aplicação está no ar com deploy automático! 

Agora é só fazer `git push` e relaxar! ☕
