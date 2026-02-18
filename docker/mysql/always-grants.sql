-- File ini dijalankan SETIAP KALI MySQL server start (via --init-file)
-- Memastikan laravel_user selalu punya akses dari semua host

CREATE USER IF NOT EXISTS 'laravel_user'@'%' IDENTIFIED BY 'laravel_password';
GRANT ALL PRIVILEGES ON laravel_minishop.* TO 'laravel_user'@'%';
FLUSH PRIVILEGES;
