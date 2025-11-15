# 🔐 Configuração do GitHub - Guia Completo

## 📝 Passo a Passo Completo

### 1️⃣ Criar Repositório no GitHub

1. Acesse https://github.com/new
2. Preencha:
   - **Repository name**: `portfolio-IA` (ou outro nome)
   - **Description**: `PromptHub - Hub de Assistentes Inteligentes`
   - **Visibility**: Private ou Public (sua escolha)
3. ❌ **NÃO marque** "Initialize repository with README"
4. Clique em **"Create repository"**

### 2️⃣ Conectar Repositório Local ao GitHub

No terminal do seu projeto local:

```bash
cd c:\xampp\htdocs\repositories\portfolio-IA

# Inicializar git (se ainda não fez)
git init

# Adicionar todos os arquivos
git add .

# Fazer primeiro commit
git commit -m "feat: initial commit with complete PromptHub application"

# Adicionar remote (SUBSTITUA seu-usuario pelo seu GitHub username)
git remote add origin https://github.com/seu-usuario/portfolio-IA.git

# Verificar se foi adicionado
git remote -v

# Criar branch main (se necessário)
git branch -M main

# Fazer push inicial
git push -u origin main
```

### 3️⃣ Configurar Secrets no GitHub

1. Acesse seu repositório no GitHub
2. Vá em **Settings** (⚙️ no topo)
3. No menu lateral esquerdo: **Secrets and variables** → **Actions**
4. Clique em **"New repository secret"**

Configure os seguintes secrets:

#### Secret 1: SERVER_HOST
```
Name: SERVER_HOST
Value: IP do seu servidor Hostinger (ex: 123.456.789.10)
```
Clique em **"Add secret"**

#### Secret 2: SERVER_USERNAME
```
Name: SERVER_USERNAME
Value: root
(ou seu usuário SSH, geralmente é root)
```
Clique em **"Add secret"**

#### Secret 3: SERVER_PORT
```
Name: SERVER_PORT
Value: 22
(porta SSH padrão)
```
Clique em **"Add secret"**

#### Secret 4: APP_PATH
```
Name: APP_PATH
Value: /root/portfolio-IA
(ou o caminho onde você vai clonar no servidor)
```
Clique em **"Add secret"**

#### Secret 5: SSH_PRIVATE_KEY (MAIS IMPORTANTE)

##### No seu computador local, gere uma chave SSH (se ainda não tem):

**Windows (PowerShell):**
```powershell
# Gerar chave SSH
ssh-keygen -t rsa -b 4096 -C "seu-email@example.com"

# Quando perguntar onde salvar, pressione ENTER (usa o padrão)
# Quando pedir senha, pode deixar vazio (pressione ENTER 2x)

# Ver a chave privada (copie todo o conteúdo)
Get-Content ~/.ssh/id_rsa
```

**Linux/Mac:**
```bash
# Gerar chave SSH
ssh-keygen -t rsa -b 4096 -C "seu-email@example.com"

# Ver a chave privada
cat ~/.ssh/id_rsa
```

##### Copiar chave pública para o servidor:

**Método 1 - Automático:**
```bash
ssh-copy-id root@seu-servidor-ip
```

**Método 2 - Manual:**
```bash
# 1. Ver sua chave pública
cat ~/.ssh/id_rsa.pub

# 2. Conectar no servidor
ssh root@seu-servidor-ip

# 3. Adicionar sua chave
mkdir -p ~/.ssh
echo "COLE_SUA_CHAVE_PUBLICA_AQUI" >> ~/.ssh/authorized_keys
chmod 700 ~/.ssh
chmod 600 ~/.ssh/authorized_keys
exit
```

##### Adicionar chave privada no GitHub:

1. No GitHub: **New repository secret**
2. **Name**: `SSH_PRIVATE_KEY`
3. **Value**: Cole TODO o conteúdo da chave privada (incluindo as linhas BEGIN e END)

```
-----BEGIN OPENSSH PRIVATE KEY-----
b3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAABlwAAAAdzc2gtcn
... (várias linhas) ...
-----END OPENSSH PRIVATE KEY-----
```

4. Clique em **"Add secret"**

### 4️⃣ Habilitar GitHub Actions

1. No repositório, vá em **Settings**
2. Menu lateral: **Actions** → **General**
3. Em **Actions permissions**, selecione:
   - ✅ **"Allow all actions and reusable workflows"**
4. Role até **Workflow permissions**
5. Selecione:
   - ✅ **"Read and write permissions"**
6. Marque:
   - ✅ **"Allow GitHub Actions to create and approve pull requests"**
7. Clique em **"Save"**

### 5️⃣ Verificar Configuração

#### Verificar Secrets:
1. Vá em **Settings** → **Secrets and variables** → **Actions**
2. Você deve ver 5 secrets:
   - ✅ `SERVER_HOST`
   - ✅ `SERVER_USERNAME`
   - ✅ `SERVER_PORT`
   - ✅ `APP_PATH`
   - ✅ `SSH_PRIVATE_KEY`

#### Testar Conexão SSH:
No seu computador, teste se consegue conectar:
```bash
ssh -i ~/.ssh/id_rsa root@seu-servidor-ip
```

Se conectar sem pedir senha, está correto! ✅

### 6️⃣ Preparar o Servidor

Conecte no servidor e prepare o ambiente:

```bash
# Conectar no servidor
ssh root@seu-servidor-ip

# Instalar Docker (se não tiver)
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh

# Instalar Docker Compose
curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose

# Verificar
docker --version
docker-compose --version

# Instalar Git (se não tiver)
apt update && apt install -y git

# Clonar o repositório (SUBSTITUA seu-usuario)
cd /root
git clone https://github.com/seu-usuario/portfolio-IA.git
cd portfolio-IA

# Configurar .env de produção
cp .env.production backend/.env
nano backend/.env
# Edite conforme necessário

# Dar permissão ao script
chmod +x deploy.sh

# Primeiro deploy manual
./deploy.sh

# Gerar chaves
docker exec prompthub-backend php artisan key:generate
docker exec prompthub-backend php artisan jwt:secret

# Atualizar .env com as chaves geradas
nano backend/.env
# Cole APP_KEY e JWT_SECRET

# Reiniciar
docker-compose restart backend
```

### 7️⃣ Testar Deploy Automático

No seu computador local:

```bash
# Fazer uma mudança qualquer
echo "# Test" >> README.md

# Commit e push
git add .
git commit -m "test: testing CI/CD"
git push origin main
```

#### Ver o Deploy em Ação:

1. No GitHub, vá em **Actions** (topo do repositório)
2. Você verá um workflow rodando
3. Clique nele para ver os logs em tempo real
4. Aguarde até aparecer ✅ **"Deploy completed successfully!"**

### 8️⃣ Proteger Branch Main (Recomendado)

1. **Settings** → **Branches**
2. Clique em **"Add rule"**
3. **Branch name pattern**: `main`
4. Marque:
   - ✅ **Require a pull request before merging**
   - ✅ **Require status checks to pass before merging**
5. Clique em **"Create"**

## 🎯 Resumo dos Secrets Necessários

| Secret Name | Descrição | Exemplo |
|-------------|-----------|---------|
| `SERVER_HOST` | IP do servidor | `123.456.789.10` |
| `SERVER_USERNAME` | Usuário SSH | `root` |
| `SERVER_PORT` | Porta SSH | `22` |
| `SSH_PRIVATE_KEY` | Chave privada SSH completa | `-----BEGIN OPENSSH...` |
| `APP_PATH` | Caminho no servidor | `/root/portfolio-IA` |

## ✅ Checklist Final

Antes de fazer o primeiro push:

- [ ] Repositório criado no GitHub
- [ ] Git inicializado localmente
- [ ] Remote adicionado
- [ ] Todos os 5 secrets configurados
- [ ] SSH funcionando sem senha
- [ ] GitHub Actions habilitado
- [ ] Servidor preparado (Docker, Git)
- [ ] Repositório clonado no servidor
- [ ] `.env` configurado no servidor
- [ ] Primeiro deploy manual executado

## 🚨 Troubleshooting

### Erro: "Permission denied (publickey)"
- Verifique se a chave SSH está correta
- Teste SSH manual: `ssh -i ~/.ssh/id_rsa root@servidor`
- Verifique se a chave pública está em `~/.ssh/authorized_keys` no servidor

### Erro: "Host key verification failed"
- Conecte uma vez manualmente no servidor para aceitar a chave
- Ou adicione no workflow: `script_stop: true`

### Erro: "docker command not found"
- Docker não está instalado no servidor
- Instale: `curl -fsSL https://get.docker.com | sh`

### Workflow não executa
- Verifique em Settings → Actions se está habilitado
- Verifique se o arquivo está em `.github/workflows/deploy.yml`
- Verifique se fez push para a branch `main`

## 🎉 Pronto!

Agora você tem CI/CD completo! 

**Fluxo de trabalho:**
1. Faça mudanças no código localmente
2. `git add . && git commit -m "descrição"`
3. `git push origin main`
4. GitHub Actions executa automaticamente
5. Deploy feito no servidor! 🚀

## 📞 Ajuda Adicional

Se precisar de ajuda:
1. Verifique os logs em **Actions** no GitHub
2. Verifique os logs do servidor: `docker-compose logs`
3. Teste SSH manualmente primeiro

---

**Última atualização:** 15/11/2025
