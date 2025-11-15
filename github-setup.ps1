# Script de Setup Inicial do GitHub
# Execute: .\github-setup.ps1

Write-Host "🚀 PromptHub - Setup do GitHub" -ForegroundColor Cyan
Write-Host "================================`n" -ForegroundColor Cyan

# 1. Verificar se está no diretório correto
$currentPath = Get-Location
Write-Host "📂 Diretório atual: $currentPath`n" -ForegroundColor Yellow

# 2. Inicializar Git
Write-Host "📦 Inicializando Git..." -ForegroundColor Green
git init

# 3. Adicionar todos os arquivos
Write-Host "➕ Adicionando arquivos..." -ForegroundColor Green
git add .

# 4. Fazer commit inicial
Write-Host "💾 Fazendo commit inicial..." -ForegroundColor Green
git commit -m "feat: initial commit with complete PromptHub application"

# 5. Pedir URL do repositório
Write-Host "`n📝 Cole a URL do seu repositório GitHub" -ForegroundColor Yellow
Write-Host "Exemplo: https://github.com/seu-usuario/portfolio-IA.git" -ForegroundColor Gray
$repoUrl = Read-Host "URL"

# 6. Adicionar remote
Write-Host "`n🔗 Conectando ao GitHub..." -ForegroundColor Green
git remote add origin $repoUrl

# 7. Verificar
Write-Host "`n✅ Repositórios remotos configurados:" -ForegroundColor Green
git remote -v

# 8. Criar/mudar para branch main
Write-Host "`n🌿 Configurando branch main..." -ForegroundColor Green
git branch -M main

# 9. Fazer push
Write-Host "`n🚀 Fazendo push para o GitHub..." -ForegroundColor Green
Write-Host "Você pode precisar fazer login no GitHub..." -ForegroundColor Yellow
git push -u origin main

Write-Host "`n✅ Setup concluído!" -ForegroundColor Green
Write-Host "`n📋 Próximos passos:" -ForegroundColor Cyan
Write-Host "1. Configure os Secrets no GitHub (veja GITHUB_SETUP.md)" -ForegroundColor White
Write-Host "2. Habilite GitHub Actions" -ForegroundColor White
Write-Host "3. Prepare o servidor" -ForegroundColor White
Write-Host "4. Faça o primeiro deploy!" -ForegroundColor White
Write-Host "`n📖 Leia o guia completo em: GITHUB_SETUP.md`n" -ForegroundColor Yellow
