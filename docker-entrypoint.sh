#!/bin/bash
set -e

echo "=========================================="
echo "Starting Laravel Application on Railway"
echo "=========================================="

# Railway provides PORT env variable
PORT=${PORT:-80}

echo "Configuring Apache to listen on port ${PORT}..."

# Configure Apache ports
cat > /etc/apache2/ports.conf << EOF
Listen ${PORT}
<IfModule ssl_module>
    Listen 443
</IfModule>
<IfModule mod_gnutls.c>
    Listen 443
</IfModule>
EOF

# Configure default virtual host
cat > /etc/apache2/sites-available/000-default.conf << EOF
<VirtualHost *:${PORT}>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

# Wait for database if configured
if [ -n "$DB_HOST" ]; then
    echo "Waiting for database at $DB_HOST..."
    max_attempts=30
    attempt=0

    until php -r "new PDO('mysql:host=${DB_HOST};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
        attempt=$((attempt + 1))
        if [ $attempt -ge $max_attempts ]; then
            echo "⚠ Could not connect to database after $max_attempts attempts"
            break
        fi
        echo "⏳ Database not ready, waiting... (attempt $attempt/$max_attempts)"
        sleep 2
    done

    if [ $attempt -lt $max_attempts ]; then
        echo "✓ Database ready, running migrations..."
        php artisan migrate --force || echo "⚠ Migrations failed or already applied"
    fi
else
    echo "ℹ No database configured (DB_HOST not set)"
fi

# Clear & cache Laravel
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link if missing
php artisan storage:link 2>/dev/null || echo "ℹ Storage link exists"

echo "Laravel version: $(php artisan --version)"
echo "=========================================="
echo "✓ Application ready, starting Apache..."
echo "=========================================="

# Start Apache in foreground
exec apache2-foreground
