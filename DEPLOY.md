# Deploy Guide - Easypanel + GitHub Actions

## 📋 Pré-requisitos

1. Servidor na Hostinger com Easypanel instalado
2. Repositório GitHub configurado
3. Acesso SSH ao servidor

## 🔧 Configuração Inicial no Servidor

### 1. Conectar ao servidor via SSH

```bash
ssh usuario@seu-servidor-hostinger.com
```

### 2. Clonar o repositório

```bash
cd /home/seu-usuario/
git clone https://github.com/seu-usuario/portfolio-IA.git
cd portfolio-IA
```

### 3. Configurar ambiente de produção

```bash
cp .env.production backend/.env
```

Edite o arquivo `backend/.env` e configure:
- `APP_KEY` - Gere com: `docker exec prompthub-backend php artisan key:generate`
- `JWT_SECRET` - Gere com: `docker exec prompthub-backend php artisan jwt:secret`
- `APP_URL` - Seu domínio (ex: https://prompthub.seudominio.com)
- `FRONTEND_URL` - Seu domínio frontend
- `GOOGLE_REDIRECT_URI` - https://seudominio.com/api/auth/google/callback

### 4. Dar permissão ao script de deploy

```bash
chmod +x deploy.sh
```

### 5. Executar o primeiro deploy

```bash
./deploy.sh
```

## 🔑 Configuração dos Secrets no GitHub

Vá em: **Settings** → **Secrets and variables** → **Actions** → **New repository secret**

Adicione os seguintes secrets:

| Secret Name | Descrição | Exemplo |
|-------------|-----------|---------|
| `SERVER_HOST` | IP ou domínio do servidor | `123.456.789.10` |
| `SERVER_USERNAME` | Usuário SSH | `root` ou seu usuário |
| `SERVER_PORT` | Porta SSH (geralmente 22) | `22` |
| `SSH_PRIVATE_KEY` | Chave privada SSH | Cole o conteúdo de `~/.ssh/id_rsa` |
| `APP_PATH` | Caminho completo do app no servidor | `/home/usuario/portfolio-IA` |

### Como gerar chave SSH (se necessário)

No seu computador local:

```bash
ssh-keygen -t rsa -b 4096 -C "seu-email@example.com"
```

Copie a chave pública para o servidor:

```bash
ssh-copy-id usuario@seu-servidor.com
```

Cole a chave privada (`~/.ssh/id_rsa`) no secret `SSH_PRIVATE_KEY` do GitHub.

## 🚀 Workflow de Deploy

Após configurar os secrets, o deploy automático funcionará assim:

1. **Push para main** → Trigger automático
2. **GitHub Actions** executa o workflow
3. **SSH no servidor** via GitHub Actions
4. **Pull do código** mais recente
5. **Rebuild dos containers** Docker
6. **Migrations** executadas automaticamente
7. **Cache** limpo e regenerado
8. **✅ Deploy completo!**

## 🔧 Configuração do Easypanel

### 1. Criar novo projeto no Easypanel

1. Acesse o Easypanel
2. Clique em "New Service"
3. Escolha "Docker Compose"
4. Cole o conteúdo de `docker-compose.production.yml`
5. Configure as variáveis de ambiente
6. Deploy!

### 2. Configurar domínio

1. No Easypanel, vá em **Domains**
2. Adicione seu domínio
3. Configure SSL (Let's Encrypt)
4. O Easypanel irá configurar automaticamente o proxy reverso

### 3. Configurar variáveis de ambiente

No Easypanel, adicione as variáveis do arquivo `.env.production`:
- `APP_KEY`
- `JWT_SECRET`
- `GOOGLE_CLIENT_ID`
- `GOOGLE_CLIENT_SECRET`
- Credenciais do banco de dados
- Redis credentials

## 📝 Comandos Úteis

### Ver logs dos containers

```bash
docker-compose -f docker-compose.production.yml logs -f
docker-compose -f docker-compose.production.yml logs backend
docker-compose -f docker-compose.production.yml logs frontend
docker-compose -f docker-compose.production.yml logs queue-worker
```

### Executar comandos no container

```bash
docker exec -it prompthub-backend bash
docker exec prompthub-backend php artisan migrate
docker exec prompthub-backend php artisan queue:work
```

### Reiniciar serviços específicos

```bash
docker-compose -f docker-compose.production.yml restart backend
docker-compose -f docker-compose.production.yml restart queue-worker
docker-compose -f docker-compose.production.yml restart frontend
```

### Rebuild completo

```bash
docker-compose -f docker-compose.production.yml down
docker-compose -f docker-compose.production.yml up -d --build
```

## 🔒 Segurança

1. **Firewall**: Configure apenas portas necessárias (80, 443, 22)
2. **SSL**: Use Let's Encrypt via Easypanel
3. **Environment**: Nunca commite `.env` com credenciais reais
4. **SSH Keys**: Use autenticação por chave, não senha
5. **Backups**: Configure backups automáticos do banco de dados

## 🐛 Troubleshooting

### Container não inicia

```bash
docker-compose -f docker-compose.production.yml logs backend
```

### Erro de permissão

```bash
docker exec prompthub-backend chmod -R 775 storage bootstrap/cache
docker exec prompthub-backend chown -R www-data:www-data storage bootstrap/cache
```

### Deploy falha no GitHub Actions

1. Verifique os secrets estão corretos
2. Teste SSH manualmente: `ssh usuario@servidor`
3. Verifique os logs do GitHub Actions

## 📞 Suporte

Se encontrar problemas:
1. Verifique os logs dos containers
2. Verifique conectividade SSH
3. Verifique configuração dos secrets no GitHub
4. Verifique se as portas estão abertas no firewall

## 🎉 Pronto!

Agora toda vez que você fizer push para `main`, o deploy será automático! 🚀
