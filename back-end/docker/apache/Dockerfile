FROM php:8.2-apache-buster

# Update and install required packages
RUN apt-get update && apt-get install -y \
        libzip-dev \
        unzip \
        libicu-dev \
    && docker-php-ext-install -j$(nproc) zip intl pdo_mysql

# Copy vhost configuration
COPY ./docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Enable Apache modules
RUN a2enmod rewrite headers

# Copy application files
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
