# Base image
FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Redis
RUN pecl install redis && docker-php-ext-enable redis

# Enable Apache modules
RUN a2enmod rewrite headers

# 🔥 CHANGE APACHE PORT TO 8000
RUN sed -i 's/80/8000/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Laravel public dir
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-interaction --optimize-autoloader --no-dev

COPY . .

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Optional: reduce log noise
RUN echo "error_reporting=E_ALL & ~E_DEPRECATED & ~E_NOTICE" > /usr/local/etc/php/conf.d/custom.ini

# ✅ Now this matches Apache
EXPOSE 8000

CMD ["apache2-foreground"]
