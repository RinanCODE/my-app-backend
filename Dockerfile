# Backend (Laravel) Dockerfile
FROM php:8.2-fpm-alpine AS php

# Install system deps
RUN apk add --no-cache bash git unzip libpng-dev oniguruma-dev libxml2-dev icu-dev libzip-dev curl

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd intl zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . /var/www/html

# Install dependencies
RUN composer install --no-interaction --prefer-dist --no-dev \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# PHP-FPM config
EXPOSE 9000
CMD ["php-fpm"]
