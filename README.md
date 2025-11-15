# 🚀 PromptHub - Hub de Assistentes Inteligentes

Plataforma completa para criar e gerenciar agentes de IA personalizados com suporte para OpenAI e Google Gemini.

[![Deploy](https://github.com/seu-usuario/portfolio-IA/actions/workflows/deploy.yml/badge.svg)](https://github.com/seu-usuario/portfolio-IA/actions/workflows/deploy.yml)

## 🎯 Funcionalidades

- ✅ **Autenticação Completa** - Login/cadastro tradicional e Google OAuth
- ✅ **Prompt Direto** - Envie prompts diretamente para OpenAI ou Gemini
- ✅ **Agentes Personalizados** - Crie agentes com comportamentos específicos
- ✅ **Contexto de Arquivos** - Envie arquivos e dados adicionais aos agentes
- ✅ **Configuração Avançada** - Ajuste temperatura, max tokens e mais
- ✅ **Interface Moderna** - Design responsivo e intuitivo

## 🐳 Executar com Docker

### Pré-requisitos
- Docker Desktop instalado
- Docker Compose
- MySQL rodando localmente (XAMPP, WAMP, etc.)

### 1. Configurar Variáveis de Ambiente

Copie o arquivo `.env.docker` para `backend/.env`:

```bash
cp .env.docker backend/.env
```

Edite `backend/.env` e ajuste as credenciais do MySQL local:
- `DB_HOST=host.docker.internal` (já configurado para acessar o host)
- `DB_DATABASE` - Nome do banco de dados
- `DB_USERNAME` - Usuário do MySQL (geralmente `root`)
- `DB_PASSWORD` - Senha do MySQL
- `GOOGLE_CLIENT_ID` - Sua chave do Google OAuth
- `GOOGLE_CLIENT_SECRET` - Seu secret do Google OAuth

### 2. Build e Iniciar Containers

```bash
docker-compose up -d --build
```

### 3. Configurar Backend

```bash
# Entrar no container do backend
docker exec -it prompthub-backend bash

# Rodar migrations
php artisan migrate

# Gerar JWT secret (se não fez no .env)
php artisan jwt:secret

# Sair do container
exit
```

### 4. Acessar a Aplicação

- **Frontend**: http://localhost
- **Backend API**: http://localhost:8000
- **MySQL**: Seu servidor local (XAMPP/WAMP)

## 📦 Executar Localmente (Sem Docker)

### Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate
php artisan serve
```

### Frontend

```bash
cd frontend
npm install
npm run dev
```

## 🔑 Obter Credenciais do Google OAuth

1. Acesse [Google Cloud Console](https://console.cloud.google.com/)
2. Crie um projeto
3. Vá em "APIs & Services" > "Credentials"
4. Clique em "Create Credentials" > "OAuth Client ID"
5. Configure:
   - Application type: Web application
   - Authorized redirect URIs: `http://localhost:8000/api/auth/google/callback`
6. Copie Client ID e Client Secret para o `.env`

## 🔧 Comandos Docker Úteis

```bash
# Ver logs
docker-compose logs -f

# Parar containers
docker-compose down

# Parar e remover volumes
docker-compose down -v

# Reconstruir containers
docker-compose up -d --build

# Entrar no container do backend
docker exec -it prompthub-backend bash

# Entrar no container do frontend
docker exec -it prompthub-frontend sh

# Ver status dos containers
docker-compose ps
```

## 🗂️ Estrutura do Projeto

```
portfolio-IA/
├── backend/              # Laravel API
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── routes/
│   └── Dockerfile
├── frontend/            # Vue.js SPA
│   ├── src/
│   ├── public/
│   ├── Dockerfile
│   └── nginx.conf
├── docker-compose.yml
└── README.md
```

## 🛠️ Tecnologias

### Backend
- **Laravel 12** - Framework PHP
- **JWT Auth** - Autenticação via tokens
- **MySQL 8** - Banco de dados
- **Google OAuth** - Login social

### Frontend
- **Vue 3** - Framework JavaScript
- **Vue Router** - Roteamento
- **Vite** - Build tool
- **Nginx** - Servidor web (produção)

### APIs de IA
- **OpenAI** - GPT-3.5 Turbo, GPT-4
- **Google Gemini** - Gemini 2.0 Flash

## 📝 Licença

Este projeto é de código aberto.

## 🚀 Deploy para Produção

Para fazer deploy em produção com CI/CD automático, consulte o guia completo: **[DEPLOY.md](DEPLOY.md)**

### Quick Deploy

1. Configure os secrets no GitHub (ver DEPLOY.md)
2. Push para a branch `main`
3. O GitHub Actions fará o deploy automaticamente! 🎉

## 👨‍💻 Autor

Desenvolvido com ❤️ para facilitar o uso de IA.
