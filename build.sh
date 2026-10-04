#!/bin/bash

echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "Building frontend assets..."
npm ci
npm run build

echo "Build completed!"
