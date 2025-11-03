# Changelog

## 2025-11-03

### Bug Fixes

- Fix ParseError di `resources/views/admin/donations/show.blade.php`:
  - Mengubah `Storage\::` menjadi `\Storage::` agar sintaks facade benar.
  - Dampak: menghilangkan "Internal Server Error: ParseError" pada halaman detail donasi admin.

- Perbaikan tampilan bukti transfer (payment proof) di admin:
  - Mengganti pemanggilan menjadi `\Storage::disk('public')->url(...)` dan `exists(...)` untuk memastikan file di `storage/app/public` terdeteksi dan URL publik terbentuk benar.
  - Menjaga fallback `public/serve-file.php` agar tetap berfungsi di hosting tanpa symlink.
  - Catatan verifikasi: direktori `public/storage/payment_proofs` dan `storage/app/public/payment_proofs` sebelumnya kosong, mohon unggah ulang bukti transfer agar data tersedia.

- Hilangkan peringatan "informasi tidak aman" pada form:
  - Menambahkan `URL::forceScheme('https')` di `AppServiceProvider` saat `APP_URL` berawalan `https`.
  - Instruksi: set `.env` `APP_URL=https://donasi-apps.test` dan restart server.

### Files Terkait

- `app/Providers/AppServiceProvider.php`
- `resources/views/admin/donations/show.blade.php`
- `public/serve-file.php` (fallback tetap tersedia, tidak diubah)

### Catatan Tambahan

- Symlink `public/storage` sudah ada (hasil `php artisan storage:link`).
- Jika upload masih gagal, cek ukuran/format (maks 2MB; `jpeg,jpg,png,pdf`).
- Halaman uji: `https://donasi-apps.test/donations/{id}/payment-detail`.