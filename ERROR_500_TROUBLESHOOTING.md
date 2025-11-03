# Troubleshooting Error 500 - PHP 8.2

## Langkah-langkah Debugging

### 1. Test PHP Basic
Akses: `https://juaraapps.my.id/test.php`
- Jika muncul "PHP is working!" = PHP berfungsi
- Jika error = ada masalah dengan PHP

### 2. Check PHP Info
Akses: `https://juaraapps.my.id/phpinfo.php`
- Cek versi PHP
- Cek ekstensi yang diperlukan
- Cek permission folder

### 3. Coba File .htaccess Berbeda

**Step 1: Gunakan .htaccess-test**
```bash
# Rename file .htaccess yang ada
mv .htaccess .htaccess-backup

# Copy file test
cp .htaccess-test .htaccess
```

**Step 2: Jika masih error, coba .htaccess-php82-fixed**
```bash
cp .htaccess-php82-fixed .htaccess
```

**Step 3: Jika masih error, coba tanpa .htaccess**
```bash
# Hapus .htaccess sementara
rm .htaccess
```

### 4. Check File Permissions
```bash
chmod 755 storage/
chmod 755 bootstrap/cache/
chmod 644 .env
chmod 644 public/.htaccess
```

### 5. Check .env File
Pastikan file `.env` ada dan berisi:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://juaraapps.my.id
```

### 6. Clear Laravel Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### 7. Reinstall Dependencies
```bash
composer install --optimize-autoloader --no-dev
```

### 8. Generate New Key
```bash
php artisan key:generate
```

## Kemungkinan Penyebab Error 500

### 1. PHP Version Mismatch
- Hosting menggunakan PHP versi lama
- Solusi: Coba file `.htaccess-php81` atau `.htaccess-php80`

### 2. Missing PHP Extensions
- BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- Solusi: Hubungi support hosting

### 3. File Permissions
- Folder storage/ dan bootstrap/cache/ tidak writable
- Solusi: Set permission 755

### 4. .env File Missing
- File .env tidak ada atau rusak
- Solusi: Copy dari .env.example

### 5. Composer Dependencies
- Vendor folder tidak lengkap
- Solusi: Jalankan composer install

### 6. Laravel Cache Corrupted
- Cache file rusak
- Solusi: Clear semua cache

## Testing Sequence

1. **Test Basic PHP**: `https://juaraapps.my.id/test.php`
2. **Check PHP Info**: `https://juaraapps.my.id/phpinfo.php`
3. **Test Laravel**: `https://juaraapps.my.id/public/index.php`
4. **Test Homepage**: `https://juaraapps.my.id/`

## File .htaccess yang Tersedia

- `.htaccess-test` - Versi sederhana untuk testing
- `.htaccess-php82-fixed` - Versi lengkap untuk PHP 8.2
- `.htaccess-php81` - Untuk PHP 8.1
- `.htaccess-php80` - Untuk PHP 8.0
- `.htaccess-php74` - Untuk PHP 7.4

## Contact Support

Jika semua langkah di atas tidak berhasil:
1. Screenshot hasil dari phpinfo.php
2. Screenshot error log
3. Hubungi support hosting dengan informasi tersebut 