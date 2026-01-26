#!/bin/bash
set -e

echo "=============================="
echo "Starting Laravel with FrankenPHP..."
echo "=============================="

# Use PORT from environment variable (Railway)
PORT="${PORT:-80}"
export FRANKENPHP_CONFIG="listen :$PORT"

# Wait for DB if HOST is provided
if [ ! -z "$DB_HOST" ]; then
    echo "Waiting for $DB_HOST:${DB_PORT:-3306}..."
    until (echo > /dev/tcp/$DB_HOST/${DB_PORT:-3306}) >/dev/null 2>&1; do
        sleep 2
    done
    echo "Database is available"
fi

# Fix permissions
echo "Fixing permissions..."
mkdir -p storage/framework/{sessions,views,cache} bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Run migrations if enabled
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "Running migrations..."
    php artisan migrate --force

    if [ "${RUN_SEEDERS:-false}" = "true" ]; then
        echo "Running seeders..."
        php artisan db:seed --force
    fi
fi

# Cache config/routes for production
if [ "${APP_ENV:-production}" = "production" ]; then
    echo "Caching configuration and routes..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "Starting FrankenPHP on port $PORT..."
# The CMD from Dockerfile will take over after this script
exec "$@"
