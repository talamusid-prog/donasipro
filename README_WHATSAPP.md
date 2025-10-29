# Fitur WhatsApp Otomatis untuk Aplikasi Donasi

## Overview

Aplikasi donasi ini telah dilengkapi dengan fitur pengiriman pesan WhatsApp otomatis menggunakan WhatsApp Business API. Fitur ini akan mengirim notifikasi kepada user pada berbagai tahap proses donasi.

## Fitur yang Tersedia

### 1. Konfirmasi Donasi
- **Kapan dikirim**: Saat user berhasil membuat donasi
- **Isi pesan**: 
  - Detail donasi (nominal, kampanye, ID)
  - Metode pembayaran
  - Batas waktu pembayaran
  - Link pembayaran
  - Langkah selanjutnya

### 2. Reminder Pembayaran
- **Kapan dikirim**: 6 jam sebelum donasi expired (dapat dikonfigurasi)
- **Isi pesan**:
  - Reminder pembayaran yang belum diselesaikan
  - Sisa waktu pembayaran
  - Link pembayaran

### 3. Konfirmasi Pembayaran Berhasil
- **Kapan dikirim**: Saat admin mengkonfirmasi pembayaran
- **Isi pesan**:
  - Konfirmasi pembayaran berhasil
  - Ucapan terima kasih
  - Doa untuk donatur

## Setup Konfigurasi

### 1. Environment Variables

Tambahkan konfigurasi berikut ke file `.env`:

```env
# WhatsApp Business API Configuration
WHATSAPP_API_URL=https://graph.facebook.com/v18.0
WHATSAPP_API_KEY=your_whatsapp_business_api_token
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id
WHATSAPP_VERIFY_TOKEN=your_webhook_verify_token
```

### 2. Cara Mendapatkan Credentials

#### Langkah 1: Setup Meta Developer Account
1. Kunjungi [Meta for Developers](https://developers.facebook.com/)
2. Login dengan akun Facebook Anda
3. Buat aplikasi baru atau gunakan yang sudah ada

#### Langkah 2: Setup WhatsApp Business API
1. Di dashboard aplikasi, pilih "WhatsApp" dari menu produk
2. Setup WhatsApp Business API
3. Verifikasi nomor telepon bisnis Anda
4. Dapatkan Phone Number ID dan Access Token

#### Langkah 3: Konfigurasi Webhook (Opsional)
Untuk menerima notifikasi status pesan:
1. Di WhatsApp Business API dashboard
2. Setup webhook dengan URL: `https://yourdomain.com/api/whatsapp/webhook`
3. Buat Verify Token acak dan simpan di environment variable

## File yang Dibuat/Dimodifikasi

### File Baru
1. `app/Services/WhatsAppService.php` - Service utama untuk mengirim pesan WhatsApp
2. `app/Console/Commands/SendPaymentReminders.php` - Command untuk kirim reminder
3. `app/Console/Commands/UpdateExpiredDonations.php` - Command untuk update status expired
4. `app/Http/Controllers/WhatsAppWebhookController.php` - Controller untuk handle webhook (opsional)
5. `WHATSAPP_SETUP_GUIDE.md` - Panduan setup lengkap
6. `README_WHATSAPP.md` - Dokumentasi ini

### File yang Dimodifikasi
1. `config/services.php` - Menambahkan konfigurasi WhatsApp
2. `app/Http/Controllers/DonationController.php` - Menambahkan pengiriman pesan saat donasi dibuat
3. `app/Http/Controllers/Admin/DonationController.php` - Menambahkan pengiriman pesan saat pembayaran dikonfirmasi
4. `routes/web.php` - Menambahkan route webhook WhatsApp

## Command yang Tersedia

### Kirim Reminder Pembayaran
```bash
# Kirim reminder untuk donasi yang expired dalam 6 jam (default)
php artisan donations:send-reminders

# Kirim reminder untuk donasi yang expired dalam 12 jam
php artisan donations:send-reminders --hours=12
```

### Update Status Donasi Expired
```bash
php artisan donations:update-expired
```

## Setup Otomatisasi (Cron Job)

Untuk mengirim reminder secara otomatis, tambahkan ke crontab server:

```bash
# Edit crontab
crontab -e

# Tambahkan baris berikut:
# Kirim reminder setiap jam
0 * * * * cd /path/to/your/app && php artisan donations:send-reminders

# Update status expired setiap 30 menit
*/30 * * * * cd /path/to/your/app && php artisan donations:update-expired
```

## Testing

### Test Pengiriman Pesan Manual
```bash
php artisan tinker
```

```php
use App\Services\WhatsAppService;
$whatsapp = new WhatsAppService();
$donation = App\Models\Donation::first();
$whatsapp->sendDonationConfirmation($donation);
```

### Test Command
```bash
# Test reminder
php artisan donations:send-reminders --hours=24

# Test update expired
php artisan donations:update-expired
```

## Format Pesan

### Konfirmasi Donasi
```
🎉 Terima Kasih Atas Donasi Anda!

Halo [Nama Donatur],

Terima kasih telah berdonasi untuk:
[Judul Kampanye]

📋 Detail Donasi:
• Nominal: Rp [Jumlah]
• Metode Pembayaran: [Metode]
• Status: Menunggu Pembayaran
• ID Donasi: #[ID]

💬 Pesan Anda:
[Pesan donatur jika ada]

⏰ Batas Waktu Pembayaran:
[Tanggal dan Waktu]

📱 Langkah Selanjutnya:
1. Selesaikan pembayaran sebelum batas waktu
2. Upload bukti pembayaran jika diperlukan
3. Kami akan mengirim notifikasi setelah pembayaran dikonfirmasi

🔗 Link Pembayaran:
[URL Pembayaran]

Jika ada pertanyaan, silakan hubungi kami.
Terima kasih atas kebaikan hati Anda! 🙏
```

### Reminder Pembayaran
```
⏰ Reminder Pembayaran Donasi

Halo [Nama Donatur],

Donasi Anda untuk [Judul Kampanye] belum diselesaikan.

📋 Detail Donasi:
• Nominal: Rp [Jumlah]
• Sisa waktu: [X] jam
• ID Donasi: #[ID]

🔗 Link Pembayaran:
[URL Pembayaran]

Silakan selesaikan pembayaran sebelum batas waktu.
Terima kasih! 🙏
```

### Konfirmasi Pembayaran Berhasil
```
✅ Pembayaran Berhasil Dikonfirmasi!

Halo [Nama Donatur],

Pembayaran donasi Anda telah berhasil dikonfirmasi!

📋 Detail Donasi:
• Kampanye: [Judul Kampanye]
• Nominal: Rp [Jumlah]
• ID Donasi: #[ID]
• Status: Dikonfirmasi

🎉 Terima kasih atas donasi Anda!
Doa dan dukungan Anda sangat berarti bagi kami.

Semoga Allah SWT membalas kebaikan Anda dengan berlipat ganda. Aamiin 🙏
```

## Troubleshooting

### Error: "Invalid access token"
- Pastikan `WHATSAPP_API_KEY` sudah benar
- Cek apakah token masih valid di Meta Developer Console
- Regenerate token jika diperlukan

### Error: "Invalid phone number ID"
- Pastikan `WHATSAPP_PHONE_NUMBER_ID` sudah benar
- Cek di WhatsApp Business API dashboard
- Pastikan nomor telepon sudah diverifikasi

### Pesan tidak terkirim
- Cek log Laravel: `storage/logs/laravel.log`
- Pastikan nomor telepon format sudah benar (62xxx)
- Cek rate limiting dari WhatsApp API
- Pastikan aplikasi WhatsApp Business sudah disetujui

### Rate Limiting
- WhatsApp Business API memiliki batas 1000 pesan per detik
- Jika melebihi, tunggu beberapa menit sebelum kirim lagi
- Implementasikan delay antar pengiriman pesan

## Keamanan

1. **Jangan commit file .env ke repository**
2. **Gunakan environment variables untuk credentials**
3. **Monitor log untuk aktivitas mencurigakan**
4. **Gunakan HTTPS untuk webhook (jika ada)**
5. **Validasi input nomor telepon**
6. **Implementasikan rate limiting**

## Monitoring

### Log yang Tersedia
- `storage/logs/laravel.log` - Log umum aplikasi
- WhatsApp API responses dan errors
- Command execution logs

### Metrics yang Bisa Dimonitor
- Jumlah pesan terkirim per hari
- Success rate pengiriman pesan
- Error rate dan jenis error
- Response time WhatsApp API

## Support

Jika mengalami masalah:
1. Cek dokumentasi resmi WhatsApp Business API
2. Cek log aplikasi di `storage/logs/laravel.log`
3. Hubungi tim developer untuk bantuan
4. Cek status WhatsApp Business API di Meta Developer Console

## Catatan Penting

1. **WhatsApp Business API memerlukan approval dari Meta**
2. **Nomor telepon bisnis harus diverifikasi**
3. **Ada batasan rate limiting dari WhatsApp**
4. **Pesan harus mengikuti template yang disetujui**
5. **Webhook memerlukan HTTPS di production** 