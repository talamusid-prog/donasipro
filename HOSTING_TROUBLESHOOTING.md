# Panduan Troubleshooting Hosting Laravel

## Masalah Umum dan Solusi

### 1. Aplikasi Tidak Bisa Dibuka
**Gejala:** Halaman blank, error 500, atau redirect loop

**Solusi:**
1. Pastikan file `.htaccess` ada di root dan folder `public/`
2. Cek permission folder:
   ```bash
   chmod 755 storage/
   chmod 755 bootstrap/cache/
   chmod 644 .env
   ```
3. Pastikan mod_rewrite aktif di hosting
4. Coba file `.htaccess-simple` jika yang utama tidak bekerja

### 2. Error 500 Internal Server Error
**Solusi:**
1. Cek error log hosting
2. Pastikan PHP version >= 8.0
3. Pastikan ekstensi PHP yang diperlukan:
   - BCMath PHP Extension
   - Ctype PHP Extension
   - JSON PHP Extension
   - Mbstring PHP Extension
   - OpenSSL PHP Extension
   - PDO PHP Extension
   - Tokenizer PHP Extension
   - XML PHP Extension

### 3. File .env Tidak Ditemukan
**Solusi:**
1. Copy `.env.example` ke `.env`
2. Generate application key:
   ```bash
   php artisan key:generate
   ```
3. Set APP_ENV=production di .env

### 4. Database Connection Error
**Solusi:**
1. Cek konfigurasi database di `.env`
2. Pastikan database server aktif
3. Cek username, password, dan nama database

### 5. Asset (CSS/JS) Tidak Muncul
**Solusi:**
1. Jalankan `php artisan storage:link`
2. Pastikan folder `public/storage` ada
3. Cek permission folder storage

## File Konfigurasi yang Dibuat

### 1. `.htaccess` (Root)
- Mengarahkan semua request ke folder public
- Mencegah akses ke file sensitif
- Menonaktifkan directory listing

### 2. `public/.htaccess` (Updated)
- Konfigurasi Laravel standard
- Security headers
- Compression dan caching
- Mencegah akses ke file sensitif

### 3. `.htaccess-simple` (Alternatif)
- Versi sederhana untuk hosting dengan batasan
- Hanya redirect ke public folder

### 4. `web.config` (Windows/IIS)
- Untuk hosting Windows/IIS
- Konfigurasi rewrite rules

## Langkah-langkah Deployment

1. **Upload semua file** ke hosting
2. **Set permission** yang benar:
   ```bash
   chmod 755 storage/
   chmod 755 bootstrap/cache/
   chmod 644 .env
   ```
3. **Konfigurasi .env**:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   ```
4. **Install dependencies**:
   ```bash
   composer install --optimize-autoloader --no-dev
   ```
5. **Generate key**:
   ```bash
   php artisan key:generate
   ```
6. **Clear cache**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
7. **Migrate database**:
   ```bash
   php artisan migrate
   ```

## Testing

Setelah deployment, test:
1. Halaman utama: `https://yourdomain.com`
2. Login/Register: `https://yourdomain.com/login`
3. Admin panel: `https://yourdomain.com/admin/login`
4. Asset loading (CSS/JS)
5. Form submission

## Support

Jika masih ada masalah:
1. Cek error log hosting
2. Aktifkan APP_DEBUG=true sementara untuk debugging
3. Hubungi support hosting untuk bantuan teknis 