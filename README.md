# Donasi Apps

Aplikasi donasi online yang dibangun dengan Laravel dan Tailwind CSS. Platform ini memungkinkan pengguna untuk berdonasi ke berbagai program kemanusiaan dengan mudah dan aman.

## Fitur Utama

- 🏠 **Halaman Beranda** - Menampilkan daftar kampanye donasi dengan filter kategori
- 📋 **Detail Kampanye** - Informasi lengkap kampanye dengan progress bar dan komentar
- 💳 **Sistem Donasi** - Form donasi dengan berbagai metode pembayaran
- 👤 **Autentikasi** - Login dan register untuk pengguna
- 📊 **Dashboard** - Riwayat donasi dan profil pengguna
- 📱 **Responsive Design** - Tampilan yang optimal di desktop dan mobile

## Teknologi yang Digunakan

- **Backend**: Laravel 10
- **Frontend**: Blade Templates + Tailwind CSS
- **Database**: MySQL
- **Icons**: Lucide Icons
- **Styling**: Custom CSS dengan Tailwind

## Instalasi

1. **Clone repository**
   ```bash
   git clone <repository-url>
   cd donasi-apps
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi database**
   - Edit file `.env` dan sesuaikan konfigurasi database
   - Jalankan migration dan seeder:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Build assets**
   ```bash
   npm run build
   ```

6. **Jalankan server**
   ```bash
   php artisan serve
   ```

## Struktur Database

### Tabel Campaigns
- `id` - Primary key
- `title` - Judul kampanye
- `description` - Deskripsi kampanye
- `organization` - Nama organisasi
- `organization_logo` - Logo organisasi
- `image_url` - Gambar kampanye
- `category` - Kategori (yatim-dhuafa, medical, education, mosque)
- `target_amount` - Target dana
- `current_amount` - Dana terkumpul
- `start_date` - Tanggal mulai
- `end_date` - Tanggal berakhir
- `status` - Status kampanye (active, completed, expired)
- `is_verified` - Status verifikasi organisasi

### Tabel Donations
- `id` - Primary key
- `campaign_id` - Foreign key ke campaigns
- `user_id` - Foreign key ke users (nullable)
- `donor_name` - Nama donatur
- `donor_email` - Email donatur
- `amount` - Jumlah donasi
- `message` - Pesan/doa (nullable)
- `payment_method` - Metode pembayaran
- `payment_status` - Status pembayaran
- `is_anonymous` - Donasi anonim

## Halaman yang Tersedia

### Halaman Publik
- `/` - Beranda dengan daftar kampanye
- `/campaigns` - Daftar semua kampanye
- `/campaigns/{id}` - Detail kampanye
- `/campaigns/{id}/donate` - Form donasi
- `/about` - Tentang kami
- `/help` - Pusat bantuan

### Halaman Autentikasi
- `/login` - Halaman login
- `/register` - Halaman register
- `/profile` - Profil pengguna (perlu login)
- `/my-donations` - Riwayat donasi (perlu login)

## Metode Pembayaran

Aplikasi mendukung berbagai metode pembayaran:
- **Transfer Bank** - BCA, Mandiri, BNI, BRI
- **E-Wallet** - GoPay, OVO, DANA, LinkAja
- **QRIS** - Scan QR Code

## Kategori Kampanye

- **Yatim & Dhuafa** - Program untuk anak yatim dan keluarga kurang mampu
- **Bantuan Medis** - Bantuan kesehatan dan pengobatan
- **Pendidikan** - Beasiswa dan bantuan pendidikan
- **Masjid** - Pembangunan dan renovasi masjid

## Kontribusi

1. Fork repository
2. Buat branch fitur baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## Lisensi

Distributed under the MIT License. See `LICENSE` for more information.

## Kontak

- Email: support@donasiapps.com
- Website: https://donasiapps.com
