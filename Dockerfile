# Use the official PHP 8.1 Apache image as a base
FROM php:8.1-apache

# Set shell for subsequent RUN commands
SHELL ["/bin/bash", "-o", "pipefail", "-c"]

# Set DEBIAN_FRONTEND to noninteractive to prevent apt-get from hanging
ENV DEBIAN_FRONTEND=noninteractive

# Install system dependencies
# ADDED: build-essential, autoconf, pkg-config to compile PHP extensions
RUN apt-get update && apt-get install -y \
    build-essential \
    autoconf \
    pkg-config \
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
# ADDED: docker-php-ext-configure for bcmath (best practice)
RUN docker-php-ext-configure bcmath --enable-bcmath && \
    docker-php-ext-install \
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

# Tell Apache to listen on port 8080 instead of 80 (Koyeb standard)
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost \*:8080>/' /etc/apache2/sites-available/000-default.conf

# Expose port 8080
EXPOSE 8080

# Set the working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# --- FIX: Reverted optimization ---
# We now copy ALL files first, then run composer install.
# This works even if composer.lock is not in your repository.
COPY . .

# Run composer install to get PHP dependencies
RUN composer install --no-dev --no-interaction --no-scripts --no-progress

# FIX: Set correct permissions for the Apache user
# This ensures Apache can write to logs/cache (if any) and own all files
RUN chown -R www-data:www-data /var/www/html

# Default command to run Apache
CMD ["apache2-foreground"]
