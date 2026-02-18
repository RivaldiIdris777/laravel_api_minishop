-- File ini dijalankan otomatis oleh MySQL saat container pertama kali start
-- Memberikan akses penuh ke laravel_user dari semua host (%)

CREATE USER IF NOT EXISTS 'laravel_user'@'%' IDENTIFIED BY 'laravel_password';
GRANT ALL PRIVILEGES ON laravel_minishop.* TO 'laravel_user'@'%';
FLUSH PRIVILEGES;
