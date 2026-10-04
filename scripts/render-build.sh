#!/usr/bin/env bash
# Render.com Build Script

set -e

echo "-----> Installing PHP dependencies..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

echo "-----> Installing Node.js dependencies..."
npm ci --only=production

echo "-----> Building frontend assets..."
npm run build

echo "-----> Setting up Laravel..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "-----> Build completed successfully!"
