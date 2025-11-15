# ✅ Checklist de Deploy - PromptHub

Use este checklist para garantir que tudo está configurado corretamente antes do deploy.

## 📝 Pré-Deploy

### Código
- [ ] Código testado localmente
- [ ] Todos os bugs conhecidos corrigidos
- [ ] Migrations testadas
- [ ] `.env.production` configurado (sem commit!)
- [ ] `.gitignore` atualizado

### GitHub
- [ ] Repositório criado no GitHub
- [ ] Branch `main` protegida (Settings → Branches)
- [ ] Code review ativado (opcional)
- [ ] Secrets configurados (ver abaixo)

### Servidor
- [ ] Easypanel instalado e funcionando
- [ ] Docker funcionando no servidor
- [ ] Acesso SSH configurado
- [ ] Firewall configurado (portas 80, 443, 22, 8000, 8080)
- [ ] Domínio configurado e apontando para o servidor

### Banco de Dados
- [ ] MySQL acessível do servidor
- [ ] Credenciais testadas
- [ ] Firewall do banco permite conexão do servidor
- [ ] Backup configurado

### Configurações
- [ ] Google OAuth configurado
- [ ] URLs de callback corretas
- [ ] SSL/HTTPS configurado
- [ ] Redis acessível

## 🔐 GitHub Secrets (Obrigatórios)

Configure em: `Settings → Secrets and variables → Actions`

- [ ] `SERVER_HOST` - IP do servidor
- [ ] `SERVER_USERNAME` - Usuário SSH
- [ ] `SERVER_PORT` - Porta SSH (22)
- [ ] `SSH_PRIVATE_KEY` - Chave privada SSH
- [ ] `APP_PATH` - Caminho do app no servidor

## 🚀 Deploy no Easypanel

### Setup Inicial
- [ ] Projeto criado no Easypanel
- [ ] GitHub conectado
- [ ] Repositório selecionado
- [ ] Branch `main` selecionada
- [ ] Auto deploy ativado

### Variáveis de Ambiente
- [ ] `APP_KEY` gerado
- [ ] `JWT_SECRET` gerado
- [ ] `APP_URL` configurado com domínio real
- [ ] `FRONTEND_URL` configurado
- [ ] `GOOGLE_REDIRECT_URI` atualizado
- [ ] Credenciais do banco configuradas
- [ ] Redis configurado

### Domínio e SSL
- [ ] Domínio adicionado no Easypanel
- [ ] DNS apontando para o servidor
- [ ] SSL via Let's Encrypt configurado
- [ ] Certificado válido

### Containers
- [ ] `backend` rodando (porta 8000)
- [ ] `frontend` rodando (porta 80/443)
- [ ] `queue-worker` rodando
- [ ] `reverb` rodando (porta 8080)

## 🧪 Testes Pós-Deploy

### Health Checks
- [ ] `https://seudominio.com/api/health` retorna `200 OK`
- [ ] Frontend carrega corretamente
- [ ] Login manual funciona
- [ ] Login com Google funciona
- [ ] Redirecionamento após login OK

### Funcionalidades
- [ ] Criar agente funciona
- [ ] Enviar prompt direto funciona
- [ ] Usar agente funciona
- [ ] Upload de arquivos funciona
- [ ] Logout funciona

### Performance
- [ ] Tempo de resposta < 2s
- [ ] Frontend carrega em < 3s
- [ ] Queue worker processando jobs
- [ ] Reverb conectado (WebSocket)

### Logs
- [ ] Sem erros críticos nos logs
- [ ] Migrations executadas com sucesso
- [ ] Queue worker sem erros
- [ ] Reverb conectado

## 🔄 Teste do CI/CD

### GitHub Actions
- [ ] Workflow aparece em Actions
- [ ] Push para `main` triggera workflow
- [ ] SSH conecta com sucesso
- [ ] Deploy executa sem erros
- [ ] Aplicação atualizada automaticamente

### Easypanel Auto Deploy
- [ ] Webhook configurado
- [ ] Push triggera rebuild
- [ ] Containers reiniciados
- [ ] Nova versão no ar

## 🔒 Segurança

### Servidor
- [ ] Firewall ativo
- [ ] Apenas portas necessárias abertas
- [ ] SSH com chave, sem senha
- [ ] Fail2ban configurado (opcional)
- [ ] Atualizações automáticas (opcional)

### Aplicação
- [ ] `APP_DEBUG=false` em produção
- [ ] HTTPS ativo e forçado
- [ ] Tokens JWT com expiração
- [ ] Rate limiting ativo
- [ ] CORS configurado corretamente

### Dados
- [ ] Backups automáticos do banco
- [ ] `.env` não commitado
- [ ] Secrets seguros no GitHub
- [ ] Logs sem informações sensíveis

## 📊 Monitoramento (Opcional)

- [ ] Uptime monitor configurado
- [ ] Alertas de erro configurados
- [ ] Logs centralizados
- [ ] Métricas de performance

## 📞 Checklist Final

Antes de considerar o deploy concluído:

- [ ] Todos os items acima verificados
- [ ] Documentação atualizada
- [ ] Time informado sobre a nova versão
- [ ] Rollback plan documentado
- [ ] Suporte disponível por 24h pós-deploy

## 🎉 Deploy Completo!

Se todos os items estão marcados, parabéns! 

Seu PromptHub está no ar com CI/CD automático! 🚀

---

**Última atualização:** 15/11/2025
