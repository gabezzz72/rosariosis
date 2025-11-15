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

# Argument to allow specifying RosarioSIS version
ARG ROSARIOSIS_VERSION=master
ENV ROSARIOSIS_VERSION=${ROSARIOSIS_VERSION}

# Download and install RosarioSIS
RUN if [ "$ROSARIOSIS_VERSION" = "master" ]; then \
        curl -fsSL "https://github.com/francoisjacquet/rosariosis/archive/master.zip" -o rosariosis.zip; \
        unzip rosariosis.zip; \
        mv rosariosis-master/* .; \
        rm -rf rosariosis-master rosariosis.zip; \
    else \
        curl -fsSL "https://github.com/francoisjacquet/rosariosis/archive/refs/tags/${ROSARIOSIS_VERSION}.zip" -o rosariosis.zip; \
        unzip rosariosis.zip; \
        mv "rosariosis-${ROSARIOSIS_VERSION}/"* .; \
        rm -rf "rosariosis-${ROSARIOSIS_VERSION}" rosariosis.zip; \
    fi \
    && composer install --no-dev --no-interaction

# Copy your pre-configured config file directly into the image
# This requires 'config.inc.php' to exist in your GitHub repo root
COPY config.inc.php .

# Default command to run Apache (no entrypoint script needed)
CMD ["apache2-foreground"]
