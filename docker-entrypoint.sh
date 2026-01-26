#!/bin/bash
set -e

echo "=========================================="
echo "Starting Laravel Application on Railway"
echo "=========================================="

# Railway provides PORT env variable, default to 80
PORT=${PORT:-80}

echo "Configuring Apache to listen on port ${PORT}..."

# Update Apache ports configuration
cat > /etc/apache2/ports.conf << EOF
Listen ${PORT}
<IfModule ssl_module>
    Listen 443
</IfModule>
<IfModule mod_gnutls.c>
    Listen 443
</IfModule>
EOF

# Update default site configuration
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

echo "✓ Apache configured to listen on port ${PORT}"

# Wait for database if DB_HOST is set
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
        echo "✓ Database is ready"
        echo "Running migrations..."
        php artisan migrate --force || echo "⚠ Migrations failed or already applied"
    fi
else
    echo "ℹ No database configured (DB_HOST not set)"
fi

# Clear and cache Laravel configuration
echo "Optimizing Laravel application..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link if it doesn't exist
php artisan storage:link 2>/dev/null || echo "ℹ Storage link already exists"

# Display Laravel version
echo "Laravel version:"
php artisan --version

echo "=========================================="
echo "✓ Application ready"
echo "Starting Apache on port ${PORT}..."
echo "=========================================="

# Start Apache in foreground
exec apache2-foreground
