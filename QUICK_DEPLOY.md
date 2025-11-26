# 🚀 Deploy Rápido - Documentação Swagger no Easypanel

## ✅ O que foi configurado

1. ✅ Swagger/OpenAPI instalado e configurado (L5-Swagger)
2. ✅ Todos os endpoints documentados com anotações @OA
3. ✅ Dockerfile atualizado para gerar docs automaticamente
4. ✅ Nginx configurado para servir `/api/documentation`
5. ✅ Script de deploy criado (deploy.ps1 e deploy.sh)
6. ✅ Documentação gerada em `storage/api-docs/api-docs.json`

## 🎯 Para fazer Deploy AGORA

### Opção 1: Build e Push Manual (PowerShell)

```powershell
# Na raiz do projeto
.\deploy.ps1 v6.0.0
```

### Opção 2: Comandos Individuais

```powershell
# Backend
cd backend
docker build --no-cache -t marcioleitedev/prompthub-backend:v6.0.0 .
docker push marcioleitedev/prompthub-backend:v6.0.0
docker tag marcioleitedev/prompthub-backend:v6.0.0 marcioleitedev/prompthub-backend:latest
docker push marcioleitedev/prompthub-backend:latest
cd ..

# Frontend (se necessário atualizar)
cd frontend
docker build --no-cache -t marcioleitedev/prompthub-frontend:v6.0.0 .
docker push marcioleitedev/prompthub-frontend:v6.0.0
docker tag marcioleitedev/prompthub-frontend:v6.0.0 marcioleitedev/prompthub-frontend:latest
docker push marcioleitedev/prompthub-frontend:latest
cd ..
```

## 📋 No Easypanel (depois do push)

1. **Acesse seu Easypanel**
2. **Vá para o serviço `prompthub-backend`**
3. **Clique em "Settings" > "General"**
4. **Atualize a imagem** para `marcioleitedev/prompthub-backend:v6.0.0` (ou `latest`)
5. **Clique em "Deploy"** ou force o pull
6. **Aguarde o restart** do container (1-2 minutos)

## 🔗 Testar a Documentação

Após o deploy, acesse:

**https://api-prompthub.marcioleite.cloud/api/documentation**

Você verá a interface Swagger com:
- ✅ Authentication endpoints (register, login, logout, me, Google OAuth)
- ✅ AI Prompts endpoints (enviar prompts para OpenAI/Gemini)
- ✅ AI Agents endpoints (CRUD completo + enviar prompts)

## 🔐 Como Testar

1. Na interface Swagger, teste o endpoint `POST /api/register` para criar um usuário
2. Copie o `token` retornado
3. Clique no botão **"Authorize"** no topo
4. Digite: `Bearer SEU_TOKEN_AQUI`
5. Agora você pode testar todos os endpoints protegidos!

## 📚 Documentação Completa

- **Deploy**: `DEPLOY_EASYPANEL.md`
- **Swagger**: `SWAGGER_DOCS.md`
- **README**: Atualizado com informações sobre Swagger

## ⚙️ Variáveis de Ambiente Importantes

No Easypanel, certifique-se de ter:

```env
APP_URL=https://api-prompthub.marcioleite.cloud
FRONTEND_URL=https://marcioleite.cloud
L5_SWAGGER_USE_ABSOLUTE_PATH=true
L5_FORMAT_TO_USE_FOR_DOCS=json
```

## 🎉 Pronto!

Após seguir esses passos, sua documentação Swagger estará disponível em:

**https://api-prompthub.marcioleite.cloud/api/documentation**

---

**Dúvidas?** Consulte os arquivos:
- `DEPLOY_EASYPANEL.md` - Guia completo de deploy
- `SWAGGER_DOCS.md` - Como usar a documentação
