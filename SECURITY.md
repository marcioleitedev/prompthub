# 🔐 Guia de Segurança - PromptHub

## ⚠️ Importante: Nunca Commite Credenciais!

Este guia explica como gerenciar corretamente as credenciais da aplicação sem expô-las no repositório Git.

## 📁 Estrutura de Arquivos de Configuração

### Arquivos no Repositório (Templates)
- `.env.docker` - Template para desenvolvimento local
- `.env.production` - Template para produção
- `docker-compose.yml` - Template do Docker Compose

### Arquivos Locais (Não no Git)
- `.credentials.local` - Suas credenciais reais
- `backend/.env` - Gerado automaticamente

## 🔑 Configurando Credenciais Localmente

### 1. Copie o Arquivo de Credenciais

```bash
# Crie o arquivo .credentials.local com suas credenciais reais
cp .credentials.local.example .credentials.local
```

### 2. Edite com suas Credenciais Reais

```bash
# Edite o arquivo .credentials.local
# Adicione suas credenciais reais:
DB_HOST=SEU_IP_AQUI
DB_DATABASE=seu_database
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

REDIS_HOST=SEU_IP_AQUI
REDIS_PASSWORD=sua_senha

GOOGLE_CLIENT_ID=sua_google_client_id_aqui
GOOGLE_CLIENT_SECRET=sua_google_client_secret_aqui
```

### 3. Nunca Commite o Arquivo .credentials.local

O arquivo `.credentials.local` está no `.gitignore` e **nunca** deve ser commitado!

## 🚀 Uso em Desenvolvimento

```bash
# Use o docker-compose.yml com as variáveis do .credentials.local
docker-compose up -d
```

## 🌍 Deploy em Produção

### GitHub Secrets

Configure os seguintes secrets no GitHub:
- `SERVER_HOST` - IP do servidor
- `SERVER_USERNAME` - Usuário SSH
- `SERVER_PORT` - Porta SSH (geralmente 22)
- `SSH_PRIVATE_KEY` - Chave SSH privada
- `APP_PATH` - Caminho da aplicação no servidor

### Variáveis de Ambiente no Servidor

No servidor de produção, configure as variáveis de ambiente:

```bash
# No servidor, edite o .env.production
nano .env.production

# Adicione suas credenciais reais
DB_HOST=SEU_IP_AQUI
DB_DATABASE=seu_database
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

REDIS_HOST=SEU_IP_AQUI
REDIS_PASSWORD=sua_senha

GOOGLE_CLIENT_ID=sua_google_client_id_aqui
GOOGLE_CLIENT_SECRET=sua_google_client_secret_aqui
```

## ✅ Checklist de Segurança

- [ ] `.gitignore` inclui `backend/.env` e `.credentials.local`
- [ ] Nunca commitar arquivos com credenciais reais
- [ ] Usar templates com placeholders no repositório
- [ ] Configurar GitHub Secrets para CI/CD
- [ ] Usar `.credentials.local` para desenvolvimento local
- [ ] Rotacionar credenciais se expostas acidentalmente

## 🆘 Se Você Expôs Credenciais

Se você acidentalmente commitou credenciais:

1. **Revogue as credenciais imediatamente**
2. **Gere novas credenciais**
3. **Limpe o histórico do Git:**

```bash
# Opção 1: Usar BFG Repo-Cleaner (recomendado)
java -jar bfg.jar --replace-text passwords.txt .git

# Opção 2: Git filter-branch
git filter-branch --force --index-filter \
  "git rm --cached --ignore-unmatch .env" \
  --prune-empty --tag-name-filter cat -- --all

# Force push (cuidado!)
git push origin --force --all
```

4. **Configure corretamente os arquivos de credenciais**

## 📚 Recursos Adicionais

- [GitHub Secrets Documentation](https://docs.github.com/en/actions/security-guides/encrypted-secrets)
- [Laravel Environment Configuration](https://laravel.com/docs/configuration)
- [BFG Repo-Cleaner](https://rtyley.github.io/bfg-repo-cleaner/)
