#!/bin/bash
# Script de Build e Deploy - PromptHub para Easypanel
# Uso: ./deploy.sh [versao]
# Exemplo: ./deploy.sh v1.0.0 ou ./deploy.sh (usa 'latest')

set -e

VERSION=${1:-latest}
DOCKER_USER="marcioleitedev"

echo "🚀 PromptHub Deploy Script"
echo "=========================="
echo "📦 Versão: $VERSION"
echo ""

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Função para mostrar progresso
progress() {
    echo -e "${GREEN}✓${NC} $1"
}

# Função para mostrar erro
error() {
    echo -e "${RED}✗${NC} $1"
}

# Função para mostrar aviso
warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

# Verificar se está na raiz do projeto
if [ ! -d "backend" ] || [ ! -d "frontend" ]; then
    error "Execute este script da raiz do projeto!"
    exit 1
fi

progress "Verificando estrutura do projeto..."

# Confirmar deploy
echo ""
read -p "🤔 Deseja fazer o build e push das imagens Docker? (s/N): " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[SsYy]$ ]]; then
    warning "Deploy cancelado!"
    exit 0
fi

# Build Backend
echo ""
echo "📦 Build do Backend..."
echo "====================="

cd backend

# Verificar se o Dockerfile existe
if [ ! -f "Dockerfile" ]; then
    error "Dockerfile não encontrado no diretório backend!"
    exit 1
fi

# Build
progress "Iniciando build da imagem do backend..."
docker build --no-cache -t ${DOCKER_USER}/prompthub-backend:${VERSION} .

if [ "$VERSION" != "latest" ]; then
    progress "Taggeando como latest..."
    docker tag ${DOCKER_USER}/prompthub-backend:${VERSION} ${DOCKER_USER}/prompthub-backend:latest
fi

# Push
progress "Fazendo push da imagem para Docker Hub..."
docker push ${DOCKER_USER}/prompthub-backend:${VERSION}

if [ "$VERSION" != "latest" ]; then
    docker push ${DOCKER_USER}/prompthub-backend:latest
fi

progress "Backend build completo!"
cd ..

# Build Frontend
echo ""
echo "🎨 Build do Frontend..."
echo "====================="

cd frontend

# Verificar se o Dockerfile existe
if [ ! -f "Dockerfile" ]; then
    error "Dockerfile não encontrado no diretório frontend!"
    exit 1
fi

# Build
progress "Iniciando build da imagem do frontend..."
docker build --no-cache -t ${DOCKER_USER}/prompthub-frontend:${VERSION} .

if [ "$VERSION" != "latest" ]; then
    progress "Taggeando como latest..."
    docker tag ${DOCKER_USER}/prompthub-frontend:${VERSION} ${DOCKER_USER}/prompthub-frontend:latest
fi

# Push
progress "Fazendo push da imagem para Docker Hub..."
docker push ${DOCKER_USER}/prompthub-frontend:${VERSION}

if [ "$VERSION" != "latest" ]; then
    docker push ${DOCKER_USER}/prompthub-frontend:latest
fi

progress "Frontend build completo!"
cd ..

# Git tag (opcional)
echo ""
read -p "🏷️  Deseja criar uma tag Git para esta versão? (s/N): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[SsYy]$ ]]; then
    git add .
    git commit -m "chore: deploy version ${VERSION} with Swagger documentation" || warning "Nada para commitar"
    git tag -a ${VERSION} -m "Release ${VERSION} - Swagger documentation included"
    git push origin main
    git push origin ${VERSION}
    progress "Tag Git criada: ${VERSION}"
fi

# Resumo
echo ""
echo "✅ Deploy Completo!"
echo "==================="
echo ""
echo "📦 Imagens Docker criadas:"
echo "   - ${DOCKER_USER}/prompthub-backend:${VERSION}"
echo "   - ${DOCKER_USER}/prompthub-frontend:${VERSION}"
echo ""
echo "🔗 Próximos passos no Easypanel:"
echo "   1. Acesse seu Easypanel"
echo "   2. Atualize as imagens dos serviços para a versão: ${VERSION}"
echo "   3. Force o pull das novas imagens"
echo "   4. Aguarde o restart dos containers"
echo "   5. Acesse a documentação: https://api-prompthub.marcioleite.cloud/api/documentation"
echo ""
echo "📚 Para mais detalhes, veja: DEPLOY_EASYPANEL.md"
echo ""
