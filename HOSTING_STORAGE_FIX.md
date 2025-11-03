# 🔧 Perbaikan Storage untuk Hosting

## Masalah
File bukti transfer tidak bisa diakses di hosting dengan error 404:
```
https://juaraapps.my.id/storage/payment_proofs/payment_proof_35_1751131701.jpeg
```

## Penyebab
Hosting tidak mendukung symlink dengan baik atau file belum tersinkronisasi.

## Solusi

### 1. Upload File Perbaikan
Upload file-file berikut ke hosting:
- `public/serve-file.php`
- `public/sync-storage-for-hosting.php`
- `public/.htaccess-hosting`

### 2. Jalankan Sync Storage
Akses URL berikut di browser:
```
https://juaraapps.my.id/sync-storage-for-hosting.php
```

File ini akan:
- Menyalin semua file dari `storage/app/public/` ke `public/storage/`
- Membuat struktur folder yang diperlukan
- Test akses file

### 3. Ganti .htaccess (Opsional)
Jika masih bermasalah, ganti `public/.htaccess` dengan `public/.htaccess-hosting`:
```bash
mv public/.htaccess public/.htaccess.backup
mv public/.htaccess-hosting public/.htaccess
```

### 4. Test Akses File
Setelah sync selesai, test akses file:
```
https://juaraapps.my.id/storage/payment_proofs/payment_proof_35_1751131701.jpeg
```

### 5. Hapus File Temporary
Setelah berhasil, hapus file temporary:
```bash
rm public/sync-storage-for-hosting.php
rm public/.htaccess-hosting
```

## Fallback System
Aplikasi sudah dilengkapi dengan fallback system:
1. Coba akses via `asset('storage/...')`
2. Jika gagal, gunakan `serve-file.php?file=...`

## Troubleshooting

### File masih 404
1. Periksa apakah file ada di `storage/app/public/payment_proofs/`
2. Jalankan sync storage lagi
3. Periksa permission folder

### Permission Error
```bash
chmod 755 public/storage
chmod 755 public/storage/payment_proofs
chmod 644 public/storage/payment_proofs/*
```

### Symlink tidak berfungsi
Gunakan file server sebagai alternatif:
```
https://juaraapps.my.id/serve-file.php?file=payment_proofs/payment_proof_35_1751131701.jpeg
```

## Catatan Keamanan
- File `serve-file.php` hanya mengizinkan akses ke direktori tertentu
- Hapus file temporary setelah selesai
- Monitor akses file untuk keamanan 