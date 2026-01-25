# Stage 2: PHP Application Runtime
FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install system dependencies dan PHP extensions
RUN apk add --no-cache \
    # Build tools
    build-base \
    # MySQL/MariaDB client
    mysql-client \
    # Curl untuk health check
    curl \
    # ZIP support
    zlib-dev \
    # Image processing
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    # Git (optional, untuk debugging)
    git \
    # Supervisor untuk menjalankan queue (optional)
    supervisor

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    gd \
    zip \
    bcmath \
    opcache && \
    docker-php-ext-enable opcache

# Configure PHP untuk production
RUN echo "memory_limit = 256M" > /usr/local/etc/php/conf.d/laravel.ini && \
    echo "upload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/laravel.ini && \
    echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/laravel.ini && \
    echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/laravel.ini

# Copy Composer dari official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create non-root user untuk security
RUN addgroup -g 1000 kevinuser && \
    adduser -D -u 1000 -G kevinuser kevinuser

# Copy built assets dari frontend builder
COPY --from=frontend-builder --chown=kevinuser:kevinuser /app/public/build /var/www/html/public/build

# Copy project files
COPY --chown=kevinuser:kevinuser . /var/www/html

# Change to kevinuser
USER kevinuser

# Install PHP dependencies (production only)
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader

# Create storage directories
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views} && \
    chmod -R 775 storage bootstrap/cache

# Expose port 9000 untuk PHP-FPM
EXPOSE 9000

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD php -r "exit(file_exists('/var/www/html/storage/logs/laravel.log') ? 0 : 1);" || exit 1

# Default command - jalankan PHP-FPM
CMD ["php-fpm"]
