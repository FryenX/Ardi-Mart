# Use the official PHP image with Apache
FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libicu-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl gd

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html

# Copy public folder to Apache root
COPY public/ /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/writable

# Set Apache DocumentRoot
RUN sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Run Composer
RUN composer install

EXPOSE 80
