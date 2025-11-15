# ⚡ Quick Start - GitHub CI/CD

## 🎯 Setup em 5 Minutos

### 1️⃣ Criar Repositório no GitHub
```
https://github.com/new
→ Nome: portfolio-IA
→ Create repository
```

### 2️⃣ Conectar Local ao GitHub
```powershell
cd c:\xampp\htdocs\repositories\portfolio-IA
git init
git add .
git commit -m "feat: initial commit"
git remote add origin https://github.com/SEU-USUARIO/portfolio-IA.git
git branch -M main
git push -u origin main
```

**OU execute o script:**
```powershell
.\github-setup.ps1
```

### 3️⃣ Configurar 5 Secrets no GitHub

**Settings → Secrets and variables → Actions → New repository secret**

```
1. SERVER_HOST = 123.456.789.10 (IP do servidor)
2. SERVER_USERNAME = root
3. SERVER_PORT = 22
4. APP_PATH = /root/portfolio-IA
5. SSH_PRIVATE_KEY = (conteúdo do ~/.ssh/id_rsa)
```

### 4️⃣ Gerar Chave SSH

**Windows PowerShell:**
```powershell
ssh-keygen -t rsa -b 4096 -C "seu-email@gmail.com"
# Pressione ENTER 3x

# Ver chave privada (copie TUDO)
Get-Content ~/.ssh/id_rsa

# Ver chave pública
Get-Content ~/.ssh/id_rsa.pub
```

### 5️⃣ Adicionar Chave no Servidor

```bash
ssh root@SEU-SERVIDOR-IP

mkdir -p ~/.ssh
nano ~/.ssh/authorized_keys
# Cole a chave pública aqui

chmod 700 ~/.ssh
chmod 600 ~/.ssh/authorized_keys
exit
```

### 6️⃣ Habilitar GitHub Actions

**Settings → Actions → General**
```
✅ Allow all actions and reusable workflows
✅ Read and write permissions
✅ Allow GitHub Actions to create and approve pull requests
```
**Save**

### 7️⃣ Preparar Servidor

```bash
ssh root@SEU-SERVIDOR-IP

# Instalar Docker
curl -fsSL https://get.docker.com | sh

# Instalar Docker Compose
curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose

# Clonar repo
cd /root
git clone https://github.com/SEU-USUARIO/portfolio-IA.git
cd portfolio-IA

# Configurar .env
cp .env.production backend/.env
nano backend/.env
# Edite conforme necessário

# Primeiro deploy
chmod +x deploy.sh
./deploy.sh

# Gerar chaves
docker exec prompthub-backend php artisan key:generate
docker exec prompthub-backend php artisan jwt:secret
```

### 8️⃣ Testar Deploy Automático

```powershell
# No seu PC
echo "# Test" >> README.md
git add .
git commit -m "test: CI/CD"
git push origin main

# Ver em: https://github.com/SEU-USUARIO/portfolio-IA/actions
```

## ✅ Checklist Rápido

```
[ ] Repositório criado no GitHub
[ ] Git conectado localmente
[ ] 5 Secrets configurados
[ ] SSH funcionando
[ ] Actions habilitado
[ ] Servidor preparado
[ ] Deploy manual OK
[ ] Deploy automático testado
```

## 🚀 Pronto!

Agora todo `git push origin main` fará deploy automático!

---

**Guia completo:** GITHUB_SETUP.md
