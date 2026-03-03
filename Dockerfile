FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install dependencies + PHP extensions
RUN apk add --no-cache \
    mysql-client \
    curl \
    git \
    oniguruma-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql bcmath opcache

# PHP Configuration
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/laravel.ini && \
    echo "upload_max_filesize=100M" >> /usr/local/etc/php/conf.d/laravel.ini && \
    echo "post_max_size=100M" >> /usr/local/etc/php/conf.d/laravel.ini && \
    echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/laravel.ini && \
    echo "log_errors=On" >> /usr/local/etc/php/conf.d/laravel.ini

# OPCache (PENTING untuk performance saat multi replica)
RUN echo "opcache.enable=1" > /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini

# Copy Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create non-root user
RUN addgroup -g 1000 kevinuser && \
    adduser -D -u 1000 -G kevinuser kevinuser

# Copy project
COPY --chown=kevinuser:kevinuser . /var/www/html

USER kevinuser

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Storage permission
RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

USER root

EXPOSE 9000

CMD ["php-fpm"]