#!/bin/bash
set -e

echo "🚀 Iniciando PromptHub Backend..."

# Verificar se o diretório de trabalho está correto
cd /var/www/html

# Ajustar permissões
echo "📁 Ajustando permissões..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Limpar cache do Laravel
echo "🧹 Limpando cache..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Verificar conexão com banco
echo "🔍 Verificando conexão com banco..."
php artisan tinker --execute="DB::connection()->getPdo();" || echo "⚠️  Aviso: Não foi possível conectar ao banco"

# Rodar migrations (apenas se DB_AUTO_MIGRATE=true)
if [ "$DB_AUTO_MIGRATE" = "true" ]; then
    echo "📊 Rodando migrations..."
    php artisan migrate --force || echo "⚠️  Aviso: Erro ao rodar migrations"
fi

# Criar diretórios necessários
mkdir -p /var/log/supervisor /var/log/nginx

echo "✅ Preparação concluída!"
echo "🎯 Iniciando Supervisor..."

# Iniciar supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
