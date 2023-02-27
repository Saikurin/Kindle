# syntax=docker/dockerfile:1
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    curl \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    && docker-php-ext-install -j$(nproc) \
        intl \
        pdo_mysql \
        zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Install project dependencies
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-scripts --no-autoloader --no-progress && \
    composer clear-cache
COPY . .
RUN composer dump-autoload --no-scripts --no-dev --optimize

# Expose port 9000 for FPM
EXPOSE 9000

# Start FPM
CMD ["php-fpm"]
