#!/bin/sh
# Script ini dijalankan di dalam container MySQL untuk fix permission user
# Cara pakai: docker exec laravel-minishop-mysql sh /fix-grants.sh

echo "🔧 Fixing MySQL user grants..."

mysql -uroot -proot_password <<EOF
-- Hapus user lama jika ada dengan host restriction
DROP USER IF EXISTS 'laravel_user'@'localhost';
DROP USER IF EXISTS 'laravel_user'@'172.19.0.3';

-- Buat ulang user dengan wildcard host
CREATE USER IF NOT EXISTS 'laravel_user'@'%' IDENTIFIED BY 'laravel_password';

-- Grant semua privilege
GRANT ALL PRIVILEGES ON laravel_minishop.* TO 'laravel_user'@'%';
FLUSH PRIVILEGES;

-- Verifikasi
SELECT user, host FROM mysql.user WHERE user = 'laravel_user';
SHOW GRANTS FOR 'laravel_user'@'%';
EOF

echo "✅ Done! laravel_user now has access from all hosts."
