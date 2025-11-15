# 🐳 Quick Start - Docker

## Pré-requisito
- MySQL rodando localmente (porta 3306)

## 1. Configurar Ambiente

```bash
# Copiar arquivo de configuração
cp .env.docker backend/.env
```

Edite `backend/.env`:
- Ajuste `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` para seu MySQL local
- Adicione suas chaves do Google OAuth

## 2. Build e Iniciar

```bash
docker-compose up -d --build
```

## 3. Configurar Backend

```bash
# Entrar no container
docker exec -it prompthub-backend bash

# Rodar migrations
php artisan migrate

# Gerar JWT secret
php artisan jwt:secret

# Sair
exit
```

## 4. Acessar

- **Frontend**: http://localhost
- **Backend**: http://localhost:8000  
- **MySQL**: Seu servidor local

## Comandos Úteis

```bash
# Ver logs
docker-compose logs -f

# Parar
docker-compose down

# Rebuild
docker-compose up -d --build
```
