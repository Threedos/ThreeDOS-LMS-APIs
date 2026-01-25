#!/bin/bash
set -e

echo "=============================="
echo "Starting Laravel Entrypoint..."
echo "=============================="

# Wait for service function (uses bash /dev/tcp, no nc needed)
wait_for_service () {
    local host="$1"
    local port="$2"
    echo "Waiting for $host:$port..."
    while ! (echo > /dev/tcp/$host/$port) >/dev/null 2>&1; do
        sleep 2
    done
    echo "$host:$port is available"
}

# Wait for DB and Redis
wait_for_service "${DB_HOST:-db}" "${DB_PORT:-3306}"
wait_for_service "${REDIS_HOST:-redis}" "${REDIS_PORT:-6379}"

# Fix permissions (ONLY what Laravel needs)
echo "Fixing permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Install dependencies if missing
if [ ! -d vendor ]; then
    echo "Installing Composer dependencies..."
    composer install --no-dev --prefer-dist --optimize-autoloader
fi

# Run migrations
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "Running migrations..."
    php artisan migrate --force

    if [ "${RUN_SEEDERS:-false}" = "true" ]; then
        echo "Running seeders..."
        php artisan db:seed --force
    fi
fi

# Optimize
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting PHP-FPM..."
exec php-fpm
