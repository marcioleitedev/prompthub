#!/bin/bash

# Deploy script for production
echo "🚀 Starting deployment..."

# Pull latest changes
echo "📥 Pulling latest code..."
git pull origin main

# Stop containers
echo "🛑 Stopping containers..."
docker-compose -f docker-compose.production.yml down

# Build and start containers
echo "🏗️ Building and starting containers..."
docker-compose -f docker-compose.production.yml up -d --build

# Wait for backend to be ready
echo "⏳ Waiting for backend to be ready..."
sleep 10

# Run migrations
echo "🗄️ Running migrations..."
docker exec prompthub-backend php artisan migrate --force

# Clear and cache configs
echo "🔄 Clearing and caching configs..."
docker exec prompthub-backend php artisan config:cache
docker exec prompthub-backend php artisan route:cache
docker exec prompthub-backend php artisan view:cache

# Restart queue worker to pick up new code
echo "♻️ Restarting queue worker..."
docker-compose -f docker-compose.production.yml restart queue-worker

echo "✅ Deployment completed successfully!"
echo "🌐 Application is running at: http://localhost"
