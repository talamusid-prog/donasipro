# Panduan Fix Error 500 - LiteSpeed Server

## Masalah yang Ditemukan:
1. **BCMath** ❌ FAIL - Ekstensi PHP untuk perhitungan presisi tinggi
2. **Mbstring** ❌ FAIL - Ekstensi PHP untuk handling string multibyte

## Solusi yang Telah Dibuat:

### 1. File .htaccess yang Diperbaiki
- File `.htaccess` sudah diperbarui untuk LiteSpeed
- File `.htaccess-litespeed` tersedia sebagai alternatif

### 2. Fallback Functions
- File `app/helpers.php` sudah ditambahkan fallback functions
- BCMath dan Mbstring functions sudah dibuat manual

### 3. Konfigurasi Logging
- File `config/logging.php` sudah diperbaiki
- Mengatasi error LogManager

## Langkah-langkah Deployment:

### Step 1: Upload File yang Diperbaiki
```bash
# Upload semua file yang sudah diperbaiki
- .htaccess (sudah diperbarui)
- app/helpers.php (sudah ditambahkan fallback)
- config/logging.php (sudah diperbaiki)
```

### Step 2: Clear Laravel Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Step 3: Reinstall Dependencies
```bash
composer install --optimize-autoloader --no-dev
```

### Step 4: Generate New Key
```bash
php artisan key:generate
```

### Step 5: Set Permissions
```bash
chmod 755 storage/
chmod 755 bootstrap/cache/
chmod 644 .env
```

## Testing:

### 1. Test Basic PHP
Akses: `https://juaraapps.my.id/test.php`
- Harus muncul: "PHP is working!"

### 2. Test Laravel
Akses: `https://juaraapps.my.id/`
- Harus muncul halaman Laravel

### 3. Test PHP Info
Akses: `https://juaraapps.my.id/phpinfo.php`
- Cek apakah fallback functions bekerja

## Jika Masih Error:

### Option 1: Hubungi Support Hosting
Minta untuk mengaktifkan ekstensi:
- BCMath
- Mbstring

### Option 2: Gunakan Fallback Functions
File `app/helpers.php` sudah berisi fallback functions yang akan bekerja meskipun ekstensi tidak tersedia.

### Option 3: Downgrade PHP
Jika hosting tidak bisa mengaktifkan ekstensi, gunakan:
- `.htaccess-php81` untuk PHP 8.1
- `.htaccess-php80` untuk PHP 8.0

## File yang Tersedia:

1. **`.htaccess`** - Konfigurasi utama untuk LiteSpeed
2. **`.htaccess-litespeed`** - Alternatif khusus LiteSpeed
3. **`app/helpers.php`** - Fallback functions untuk BCMath dan Mbstring
4. **`config/logging.php`** - Konfigurasi logging yang diperbaiki
5. **`public/test.php`** - File test PHP basic
6. **`public/phpinfo.php`** - File untuk cek konfigurasi

## Expected Result:

Setelah mengikuti langkah-langkah di atas:
- ✅ PHP 8.2.28 berfungsi
- ✅ BCMath functions tersedia (via fallback)
- ✅ Mbstring functions tersedia (via fallback)
- ✅ Laravel bisa diakses
- ✅ Error 500 teratasi

## Troubleshooting:

Jika masih ada masalah:
1. Cek error log hosting
2. Pastikan semua file terupload dengan benar
3. Pastikan permission folder benar
4. Hubungi support hosting untuk bantuan teknis 