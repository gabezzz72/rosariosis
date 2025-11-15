# Use the official PHP 8.1 Apache image as a base
FROM php:8.1-apache

# Set shell for subsequent RUN commands
SHELL ["/bin/bash", "-o", "pipefail", "-c"]

# Set version to download
# You can change "master" to a specific version tag like "10.9.3"
ARG ROSARIOSIS_VERSION=custom-rosario

# Install system dependencies
RUN apt-get update && apt-get install -y \
    postgresql-client \
    libpq-dev \
    libgd-dev \
    gettext \
    libicu-dev \
    libzip-dev \
    zlib1g-dev \
    unzip \
    curl \
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

# Tell Apache to listen on port 8080 instead of 80
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost \*:8080>/' /etc/apache2/sites-available/000-default.conf

# Expose port 8080
EXPOSE 8080

# Set the working directory
WORKDIR /var/www/html

# --- NEW STRATEGY ---
# 1. Download and unzip the core RosarioSIS application
RUN curl -fsSL "https://github.com/francoisjacquet/rosariosis/archive/${ROSARIOSIS_VERSION}.zip" -o rosariosis.zip \
    && unzip rosariosis.zip \
    && mv rosariosis-${ROSARIOSIS_VERSION}/* rosariosis-${ROSARIOSIS_VERSION}/.[!.]* . \
    && rm -rf rosariosis-${ROSARIOSIS_VERSION} rosariosis.zip

# 2. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Copy YOUR files (config, custom module) over the core files
# This will overwrite the default config.inc.sample.php
# and add your modules/Assessments directory
COPY . .

# 4. Run composer install
RUN composer install --no-dev --no-interaction

# 5. Set correct permissions for the Apache user
RUN chown -R www-data:www-data /var/www/html/

# Default command to run Apache
CMD ["apache2-foreground"]
