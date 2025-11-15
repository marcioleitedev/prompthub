FROM php:8.2-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar extensão Redis
RUN pecl install redis && docker-php-ext-enable redis

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos da aplicação
COPY . .

# Limpar cache antes de instalar dependências
RUN rm -rf bootstrap/cache/*.php

# Instalar dependências do Composer (com dev para evitar erros)
RUN composer install --no-interaction --optimize-autoloader

# Copiar configuração do nginx
COPY nginx.conf /etc/nginx/sites-available/default

# Copiar configuração do supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copiar script de inicialização
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Ajustar permissões
RUN chmod -R 775 storage bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Criar diretórios de log
RUN mkdir -p /var/log/supervisor

# Expor portas (80 para web, 8080 para reverb)
EXPOSE 80 8080

# Comando para iniciar via script
CMD ["/usr/local/bin/start.sh"]
