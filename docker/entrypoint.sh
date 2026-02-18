#!/bin/sh
set -e

# Fix permission untuk storage dan bootstrap/cache
# Ini diperlukan karena volume mount dari Windows bisa override permission
mkdir -p /var/www/html/storage/logs \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/bootstrap/cache

chmod -R 777 /var/www/html/storage
chmod -R 777 /var/www/html/bootstrap/cache

echo "✅ Storage permissions fixed"

# Jalankan command yang diberikan (php-fpm atau queue:work)
exec "$@"
