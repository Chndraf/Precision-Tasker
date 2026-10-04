#!/bin/sh
set -e

echo "Starting Precision Tasker..."

# Create necessary directories
mkdir -p /var/www/html/storage/framework/{sessions,views,cache}
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# Set correct permissions
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Run migrations
echo "Running database migrations..."
php artisan migrate --force --no-interaction || echo "Migration skipped or failed"

# Clear and cache config
echo "Optimizing application..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start supervisord (which will start nginx and php-fpm)
echo "Starting services..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
