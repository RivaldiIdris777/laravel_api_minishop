# LARAVEL MINISHOP API

Dokumentasi lengkap API untuk aplikasi e-commerce minishop berbasis Laravel dengan fitur manajemen produk, kategori, pesanan, dan autentikasi.

**Base URL:** `http://127.0.0.1:8000/api`  
**Catatan:** Untuk production, ganti `http://127.0.0.1:8000` dengan domain aplikasi Anda.

---

## 📋 Daftar Isi
- [Autentikasi](#autentikasi)
- [Produk](#produk)
- [Kategori](#kategori)
- [Pesanan](#pesanan)
- [Pengguna](#pengguna)
- [Role & Permisi](#role--permisi)

---

## Autentikasi

### 1. Register User

***Endpoint:***
```bash
Method: POST
URL: http://127.0.0.1:8000/api/register
```

***Body:***

| Key | Value | Description |
| --- | ------|-------------|
| name | John Doe | Harus diisi dengan string dan wajib diisi |
| email | john@example.com | Harus diisi dengan format email yang valid dan belum terdaftar |
| password | password123 | Harus diisi dengan minimal 8 karakter dan wajib diisi |
| password_confirmation | password123 | Harus sama dengan field password |

***Response (201):***
```json
{
    "success": true,
    "message": "Success register",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2024-01-25T10:00:00Z"
    }
}
```

---

### 2. Login User

***Endpoint:***
```bash
Method: POST
URL: http://127.0.0.1:8000/api/login
```

***Body:***

| Key | Value | Description |
| --- | ------|-------------|
| email | john@example.com | Email yang terdaftar di sistem |
| password | password123 | Password akun user |

***Response (200):***
```json
{
    "success": true,
    "message": "Success login",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

---

### 3. Logout User

***Endpoint:***
```bash
Method: POST
URL: http://127.0.0.1:8000/api/logout
Headers: Authorization: Bearer {token}
```

***Response (200):***
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

---

### 4. Login dengan Google

***Endpoint:***
```bash
Method: POST
URL: http://127.0.0.1:8000/api/google/token
```

***Body:***

| Key | Value | Description |
| --- | ------|-------------|
| token | google_id_token | Token dari Google OAuth 2.0 |

***Response (200):***
```json
{
    "success": true,
    "message": "Login successful",
    "user": {...},
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

---

## Produk

### 1. Get All Products

***Endpoint:***
```bash
Method: GET
URL: http://127.0.0.1:8000/api/getproduct
```

***Response (200):***
```json
{
    "success": true,
    "message": "List Data Products",
    "data": [
        {
            "id": 1,
            "name": "Laptop Gaming",
            "slug": "laptop-gaming",
            "category_id": 1,
            "price": 15000000,
            "offer_price": 13500000,
            "short_description": "Laptop gaming berkualitas tinggi",
            "long_description": "Deskripsi lengkap produk...",
            "image": "product/image.jpg",
            "sku": "LAP-001",
            "status": "active",
            "show_at_home": true
        }
    ]
}
```

---

### 2. Get Product Detail by Slug

***Endpoint:***
```bash
Method: GET
URL: http://127.0.0.1:8000/api/detailproduct/{slug}
```

***Parameters:***

| Name | Type | Description |
| --- | --- | --- |
| slug | string | Slug produk (contoh: laptop-gaming) |

***Response (200):***
```json
{
    "success": true,
    "message": "Detail Product",
    "data": {
        "id": 1,
        "name": "Laptop Gaming",
        "slug": "laptop-gaming",
        "category_id": 1,
        "price": 15000000,
        "offer_price": 13500000,
        "short_description": "Laptop gaming berkualitas tinggi",
        "long_description": "Deskripsi lengkap produk...",
        "image": "http://127.0.0.1:8000/storage/product/image.jpg",
        "sku": "LAP-001",
        "seo_title": "Beli Laptop Gaming Terbaik",
        "seo_description": "Laptop gaming dengan spesifikasi tinggi...",
        "status": "active",
        "show_at_home": true,
        "category": {...}
    }
}
```

---

### 3. Create Product (Admin)

***Endpoint:***
```bash
Method: POST
URL: http://127.0.0.1:8000/api/product
Headers: Authorization: Bearer {token}
Content-Type: multipart/form-data
```

***Body:***

| Key | Type | Description |
| --- | --- | --- |
| name | string | Nama produk (max 200 karakter) - wajib |
| image | file | File gambar (jpeg, png, jpg, gif, svg, max 5MB) - wajib |
| category_id | integer | ID kategori - wajib |
| price | numeric | Harga jual - wajib |
| offer_price | numeric | Harga promosi - wajib |
| short_description | string | Deskripsi singkat - wajib |
| long_description | string | Deskripsi lengkap - wajib |
| sku | string | Stock Keeping Unit - wajib |
| seo_title | string | Judul SEO - wajib |
| seo_description | string | Deskripsi SEO - wajib |
| status | string | Status produk (active/inactive) - wajib |
| show_at_home | boolean | Tampilkan di halaman utama - wajib |

---

### 4. Update Product (Admin)

***Endpoint:***
```bash
Method: PUT
URL: http://127.0.0.1:8000/api/product/{id}
Headers: Authorization: Bearer {token}
```

---

### 5. Delete Product (Admin)

***Endpoint:***
```bash
Method: DELETE
URL: http://127.0.0.1:8000/api/product/{id}
Headers: Authorization: Bearer {token}
```

---

## Kategori

### 1. Get All Categories

***Endpoint:***
```bash
Method: GET
URL: http://127.0.0.1:8000/api/getcategory
```

***Response (200):***
```json
{
    "success": true,
    "message": "List Data Categories",
    "data": [
        {
            "id": 1,
            "name": "Elektronik",
            "slug": "elektronik",
            "status": "active",
            "show_at_home": true,
            "created_at": "2024-01-25T10:00:00Z"
        }
    ]
}
```

---

### 2. Create Category (Admin)

***Endpoint:***
```bash
Method: POST
URL: http://127.0.0.1:8000/api/category
Headers: Authorization: Bearer {token}
```

***Body:***

| Key | Value | Description |
| --- | --- | --- |
| name | Elektronik | Nama kategori - wajib |
| slug | elektronik | Slug kategori - wajib |
| status | active | Status kategori - wajib |
| show_at_home | true | Tampilkan di halaman utama - wajib |

***Response (200):***
```json
{
    "success": true,
    "message": "Data Category Berhasil Ditambahkan!",
    "data": {...}
}
```

---

### 3. Update Category (Admin)

***Endpoint:***
```bash
Method: PUT
URL: http://127.0.0.1:8000/api/category/{id}
Headers: Authorization: Bearer {token}
```

---

### 4. Delete Category (Admin)

***Endpoint:***
```bash
Method: DELETE
URL: http://127.0.0.1:8000/api/category/{id}
Headers: Authorization: Bearer {token}
```

---

## Pesanan

### 1. Create Order

***Endpoint:***
```bash
Method: POST
URL: http://127.0.0.1:8000/api/order
Content-Type: multipart/form-data
```

***Body:***

| Key | Type | Description |
| --- | --- | --- |
| name | string | Nama pemesan - wajib |
| email | string | Email pemesan - wajib |
| phone | string | Nomor telepon pemesan |
| city | string | Kota pengiriman |
| address | string | Alamat lengkap pengiriman |
| total_cost | numeric | Total biaya pesanan - wajib |
| payment_method | string | Metode pembayaran (contoh: credit_card, bank_transfer) |
| status | string | Status pesanan (pending, processing, completed) |
| items | JSON string | Detail items dalam format JSON |

***Items Format:***
```json
[
    {
        "product_id": 1,
        "quantity": 2,
        "price": 15000000
    }
]
```

***Response (201):***
```json
{
    "success": true,
    "message": "Order created successfully",
    "order": {
        "id": 1,
        "order_number": "ORD-2024-001",
        "name": "John Doe",
        "email": "john@example.com",
        "total_cost": 30000000,
        "status": "pending",
        "items": [...]
    }
}
```

---

### 2. Get All Orders

***Endpoint:***
```bash
Method: GET
URL: http://127.0.0.1:8000/api/orders
Headers: Authorization: Bearer {token}
```

***Response (200):***
```json
{
    "success": true,
    "message": "Order retrieved successfully",
    "orders": [
        {
            "id": 1,
            "order_number": "ORD-2024-001",
            "name": "John Doe",
            "email": "john@example.com",
            "total_cost": 30000000,
            "status": "pending",
            "items": [...]
        }
    ]
}
```

---

### 3. Get Order Count Today

***Endpoint:***
```bash
Method: GET
URL: http://127.0.0.1:8000/api/orders/today/count
Headers: Authorization: Bearer {token}
```

***Response (200):***
```json
{
    "date": "2024-01-25",
    "count": 5
}
```

---

## Pengguna

### 1. Get All Users (Admin)

***Endpoint:***
```bash
Method: GET
URL: http://127.0.0.1:8000/api/users
Headers: Authorization: Bearer {token}
```

***Response (200):***
```json
{
    "success": true,
    "message": "List Data Users",
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2024-01-25T10:00:00Z"
        }
    ]
}
```

---

## Role & Permisi

### 1. Get All Roles (Admin)

***Endpoint:***
```bash
Method: GET
URL: http://127.0.0.1:8000/api/roles
Headers: Authorization: Bearer {token}
```

---

### 2. Create Role (Admin)

***Endpoint:***
```bash
Method: POST
URL: http://127.0.0.1:8000/api/roles
Headers: Authorization: Bearer {token}
```

---

### 3. Get All Permissions (Admin)

***Endpoint:***
```bash
Method: GET
URL: http://127.0.0.1:8000/api/permissions
Headers: Authorization: Bearer {token}
```

---

## Autentikasi & Headers

Untuk mengakses endpoint yang dilindungi, tambahkan header:

```bash
Authorization: Bearer {token}
Content-Type: application/json
```

Token diperoleh dari endpoint `/login` atau `/register`.

---

## Status Codes

| Code | Meaning |
| --- | --- |
| 200 | OK - Request berhasil |
| 201 | Created - Resource berhasil dibuat |
| 400 | Bad Request - Request tidak valid |
| 401 | Unauthorized - Token tidak valid atau expired |
| 404 | Not Found - Resource tidak ditemukan |
| 422 | Unprocessable Entity - Validasi gagal |
| 500 | Server Error - Kesalahan server |

---

## Teknologi yang Digunakan

- **Framework:** Laravel 12
- **Autentikasi:** JWT (tymon/jwt-auth)
- **Authorization:** Spatie Laravel Permission
- **Social Auth:** Laravel Socialite (Google OAuth)
- **Payment Gateway:** Midtrans
- **Database:** MySQL/PostgreSQL
- **API Documentation:** RESTful API

---

## Instalasi & Setup

```bash
# Clone repository
git clone <repository-url>

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed

# Start server
php artisan serve
```

---

## Catatan Pengembangan

- Semua request harus menggunakan format JSON (kecuali upload file)
- Token JWT berlaku sesuai konfigurasi di `.env`
- Untuk production, gunakan HTTPS dan validasi CORS dengan benar
- Pastikan file `.env` dikonfigurasi dengan benar sebelum menjalankan aplikasi
- Untuk mengakses dokumentasi api via browser berikan url : urlanda:8000/docs/api
