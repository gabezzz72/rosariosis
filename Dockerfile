# Use the official PHP 8.1 Apache image as a base
FROM php:8.1-apache

# Set shell for subsequent RUN commands
SHELL ["/bin/bash", "-o", "pipefail", "-c"]

# Install system dependencies
# postgresql-client is for 'psql'
# libpq-dev, libgd-dev, gettext, etc., are for PHP extensions
RUN apt-get update && apt-get install -y \
    postgresql-client \
    libpq-dev \
    libgd-dev \
    gettext \
    libicu-dev \
    libzip-dev \
    zlib1g-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install required PHP extensions
RUN docker-php-ext-install \
    gd \
    intl \
    pdo_pgsql \
    pdo_mysql \
    pgsql \
    mysqli \
    gettext \
    zip \
    bcmath

# Install APCu for caching
RUN pecl install apcu \
    && docker-php-ext-enable apcu

# Enable opcache and rewrite module
RUN docker-php-ext-enable opcache \
    && a2enmod rewrite expires

# Set the working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy ALL application files from your repository into the image
# This includes your pre-configured config.inc.php and composer.json
COPY . .

# Run composer install to get PHP dependencies
RUN composer install --no-dev --no-interaction

# Default command to run Apache
CMD ["apache2-foreground"]
