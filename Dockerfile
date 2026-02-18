FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install build dependencies first
RUN apk add --no-cache --virtual .build-deps \
    autoconf \
    build-base \
    && apk add --no-cache \
    mysql-client \
    curl \
    git \
    oniguruma-dev

# Install PHP extensions
RUN docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    bcmath \
    && docker-php-ext-enable pdo pdo_mysql bcmath

# Remove build dependencies
RUN apk del --no-network .build-deps

# Configure PHP
RUN echo "memory_limit = 256M" > /usr/local/etc/php/conf.d/laravel.ini && \
    echo "upload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/laravel.ini && \
    echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/laravel.ini && \
    echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/laravel.ini && \
    echo "log_errors = On" >> /usr/local/etc/php/conf.d/laravel.ini

# Copy Composer dari official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create non-root user
RUN addgroup -g 1000 kevinuser && \
    adduser -D -u 1000 -G kevinuser kevinuser

# Copy project files
COPY --chown=kevinuser:kevinuser . /var/www/html

# Copy entrypoint script (sebagai root agar bisa chmod)
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Change to kevinuser untuk install composer
USER kevinuser

# Install PHP dependencies
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader

# Create storage directories
RUN mkdir -p storage/logs \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Kembali ke root agar entrypoint bisa fix permission saat start
USER root

# Expose port 9000 untuk PHP-FPM
EXPOSE 9000

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD php -r "exit(0);" || exit 1

# Gunakan entrypoint untuk fix permission, lalu jalankan php-fpm
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
