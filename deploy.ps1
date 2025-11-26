# Script de Build e Deploy - PromptHub para Easypanel (Windows PowerShell)
# Uso: .\deploy.ps1 [versao]
# Exemplo: .\deploy.ps1 v1.0.0 ou .\deploy.ps1 (usa 'latest')

param(
    [string]$Version = "latest"
)

$DOCKER_USER = "marcioleitedev"

Write-Host "🚀 PromptHub Deploy Script" -ForegroundColor Cyan
Write-Host "==========================" -ForegroundColor Cyan
Write-Host "📦 Versão: $Version" -ForegroundColor Yellow
Write-Host ""

function Write-Progress {
    param([string]$Message)
    Write-Host "✓ $Message" -ForegroundColor Green
}

function Write-Error-Custom {
    param([string]$Message)
    Write-Host "✗ $Message" -ForegroundColor Red
}

function Write-Warning-Custom {
    param([string]$Message)
    Write-Host "⚠ $Message" -ForegroundColor Yellow
}

# Verificar se está na raiz do projeto
if (-not (Test-Path "backend") -or -not (Test-Path "frontend")) {
    Write-Error-Custom "Execute este script da raiz do projeto!"
    exit 1
}

Write-Progress "Verificando estrutura do projeto..."

# Confirmar deploy
Write-Host ""
$confirm = Read-Host "🤔 Deseja fazer o build e push das imagens Docker? (s/N)"
if ($confirm -notmatch '^[SsYy]$') {
    Write-Warning-Custom "Deploy cancelado!"
    exit 0
}

# Build Backend
Write-Host ""
Write-Host "📦 Build do Backend..." -ForegroundColor Cyan
Write-Host "=====================" -ForegroundColor Cyan

Set-Location backend

# Verificar se o Dockerfile existe
if (-not (Test-Path "Dockerfile")) {
    Write-Error-Custom "Dockerfile não encontrado no diretório backend!"
    exit 1
}

# Build
Write-Progress "Iniciando build da imagem do backend..."
docker build --no-cache -t ${DOCKER_USER}/prompthub-backend:${Version} .

if ($Version -ne "latest") {
    Write-Progress "Taggeando como latest..."
    docker tag ${DOCKER_USER}/prompthub-backend:${Version} ${DOCKER_USER}/prompthub-backend:latest
}

# Push
Write-Progress "Fazendo push da imagem para Docker Hub..."
docker push ${DOCKER_USER}/prompthub-backend:${Version}

if ($Version -ne "latest") {
    docker push ${DOCKER_USER}/prompthub-backend:latest
}

Write-Progress "Backend build completo!"
Set-Location ..

# Build Frontend
Write-Host ""
Write-Host "🎨 Build do Frontend..." -ForegroundColor Cyan
Write-Host "=====================" -ForegroundColor Cyan

Set-Location frontend

# Verificar se o Dockerfile existe
if (-not (Test-Path "Dockerfile")) {
    Write-Error-Custom "Dockerfile não encontrado no diretório frontend!"
    exit 1
}

# Build
Write-Progress "Iniciando build da imagem do frontend..."
docker build --no-cache -t ${DOCKER_USER}/prompthub-frontend:${Version} .

if ($Version -ne "latest") {
    Write-Progress "Taggeando como latest..."
    docker tag ${DOCKER_USER}/prompthub-frontend:${Version} ${DOCKER_USER}/prompthub-frontend:latest
}

# Push
Write-Progress "Fazendo push da imagem para Docker Hub..."
docker push ${DOCKER_USER}/prompthub-frontend:${Version}

if ($Version -ne "latest") {
    docker push ${DOCKER_USER}/prompthub-frontend:latest
}

Write-Progress "Frontend build completo!"
Set-Location ..

# Git tag (opcional)
Write-Host ""
$gitTag = Read-Host "🏷️  Deseja criar uma tag Git para esta versão? (s/N)"
if ($gitTag -match '^[SsYy]$') {
    git add .
    try {
        git commit -m "chore: deploy version ${Version} with Swagger documentation"
    } catch {
        Write-Warning-Custom "Nada para commitar"
    }
    git tag -a ${Version} -m "Release ${Version} - Swagger documentation included"
    git push origin main
    git push origin ${Version}
    Write-Progress "Tag Git criada: ${Version}"
}

# Resumo
Write-Host ""
Write-Host "✅ Deploy Completo!" -ForegroundColor Green
Write-Host "===================" -ForegroundColor Green
Write-Host ""
Write-Host "📦 Imagens Docker criadas:"
Write-Host "   - ${DOCKER_USER}/prompthub-backend:${Version}"
Write-Host "   - ${DOCKER_USER}/prompthub-frontend:${Version}"
Write-Host ""
Write-Host "🔗 Próximos passos no Easypanel:" -ForegroundColor Yellow
Write-Host "   1. Acesse seu Easypanel"
Write-Host "   2. Atualize as imagens dos serviços para a versão: ${Version}"
Write-Host "   3. Force o pull das novas imagens"
Write-Host "   4. Aguarde o restart dos containers"
Write-Host "   5. Acesse a documentação: https://api-prompthub.marcioleite.cloud/api/documentation"
Write-Host ""
Write-Host "📚 Para mais detalhes, veja: DEPLOY_EASYPANEL.md" -ForegroundColor Cyan
Write-Host ""
