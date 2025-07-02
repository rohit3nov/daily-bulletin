FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl zip unzip git cron libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www
