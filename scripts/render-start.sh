#!/usr/bin/env bash
# Render.com Start Script

set -e

echo "-----> Starting Precision Tasker..."

# Create necessary directories
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set permissions
chmod -R 775 storage bootstrap/cache

echo "-----> Running database migrations..."
php artisan migrate --force --no-interaction || echo "Migration failed or skipped"

echo "-----> Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "-----> Starting web server..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
