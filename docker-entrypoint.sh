#!/bin/bash
set -e

echo "Starting Laravel on Railway"

PORT=${PORT:-80}

# Apache port config
cat > /etc/apache2/ports.conf << EOF
Listen ${PORT}
<IfModule ssl_module>
    Listen 443
</IfModule>
<IfModule mod_gnutls.c>
    Listen 443
</IfModule>
EOF

cat > /etc/apache2/sites-available/000-default.conf << EOF
<VirtualHost *:${PORT}>
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

# Wait for DB
if [ -n "$DB_HOST" ]; then
    echo "Waiting for DB at $DB_HOST..."
    for i in {1..30}; do
        php -r "new PDO('mysql:host=${DB_HOST};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" && break
        echo "DB not ready, retrying... ($i/30)"
        sleep 2
    done
    php artisan migrate --force || echo "⚠ Migrations failed or already applied"
fi

# Clear/cache Laravel
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage link
php artisan storage:link 2>/dev/null || echo "Storage link exists"

echo "Laravel version: $(php artisan --version)"
echo "Starting Apache on port ${PORT}"
exec apache2-foreground
