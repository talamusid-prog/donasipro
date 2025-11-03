# Panduan Kategori dan Section Campaign

## Kategori Campaign

Sistem kategori campaign telah diperluas untuk mencakup berbagai jenis program donasi:

### 1. **Yatim & Dhuafa** (`yatim-dhuafa`)
- Program untuk anak yatim dan keluarga kurang mampu
- Bantuan pendidikan, kesehatan, dan kebutuhan sehari-hari
- Contoh: Beasiswa anak yatim, bantuan keluarga dhuafa

### 2. **Bantuan Medis** (`medical`)
- Program bantuan kesehatan dan pengobatan
- Operasi, pengobatan penyakit, dan fasilitas kesehatan
- Contoh: Operasi jantung, pengobatan kanker, bantuan medis

### 3. **Pendidikan** (`education`)
- Program bantuan pendidikan dan beasiswa
- Pembangunan sekolah, beasiswa, dan sarana pendidikan
- Contoh: Beasiswa mahasiswa, pembangunan sekolah

### 4. **Masjid** (`mosque`)
- Program pembangunan dan renovasi masjid
- Fasilitas ibadah dan kegiatan keagamaan
- Contoh: Pembangunan masjid, renovasi masjid

### 5. **Bencana Alam** (`disaster`)
- Program bantuan korban bencana alam
- Bantuan darurat, rekonstruksi, dan rehabilitasi
- Contoh: Bantuan gempa, banjir, kebakaran

### 6. **Panti Asuhan** (`orphanage`)
- Program untuk panti asuhan dan lembaga pengasuhan
- Renovasi, pembangunan, dan bantuan operasional
- Contoh: Renovasi panti asuhan, bantuan operasional

### 7. **Rumah Sakit** (`hospital`)
- Program untuk fasilitas kesehatan dan rumah sakit
- Pembangunan, renovasi, dan peralatan medis
- Contoh: Pembangunan ICU, peralatan medis

### 8. **Sekolah** (`school`)
- Program khusus untuk institusi pendidikan
- Pembangunan, renovasi, dan fasilitas sekolah
- Contoh: Pembangunan SD, perpustakaan sekolah

### 9. **Masyarakat** (`community`)
- Program pemberdayaan dan pengembangan masyarakat
- Pelatihan, modal usaha, dan program sosial
- Contoh: Pemberdayaan masyarakat, pelatihan keterampilan

### 10. **Lingkungan** (`environment`)
- Program pelestarian dan perbaikan lingkungan
- Penghijauan, pengelolaan sampah, dan konservasi
- Contoh: Penghijauan hutan, pengelolaan sampah

### 11. **Hewan** (`animal`)
- Program untuk kesejahteraan hewan
- Penyelamatan, perawatan, dan konservasi hewan
- Contoh: Penyelamatan hewan terlantar, konservasi satwa

### 12. **Lainnya** (`other`)
- Program yang tidak masuk dalam kategori di atas
- Program khusus atau unik lainnya

## Section Campaign

Section menentukan di bagian mana campaign akan ditampilkan di halaman utama:

### 1. **Unggulan** (`featured`)
- Campaign unggulan yang ditampilkan di bagian atas
- Biasanya campaign dengan progress tinggi atau penting
- Maksimal 3-4 campaign per section

### 2. **Urgent** (`urgent`)
- Campaign yang membutuhkan bantuan segera
- Biasanya campaign medis atau bencana alam
- Ditampilkan dengan highlight khusus

### 3. **Populer** (`popular`)
- Campaign yang banyak diminati donatur
- Berdasarkan jumlah donasi atau engagement tinggi
- Ditampilkan di section tengah

### 4. **Baru** (`new`)
- Campaign yang baru dibuat (default section)
- Ditampilkan di section bawah
- Campaign yang belum memiliki track record

### 5. **Berakhir Segera** (`ending_soon`)
- Campaign yang akan berakhir dalam waktu dekat
- Biasanya < 30 hari tersisa
- Ditampilkan untuk mengingatkan donatur

## Cara Penggunaan di Admin Panel

### 1. **Membuat Campaign Baru**
- Pilih kategori yang sesuai dengan jenis program
- Pilih section berdasarkan prioritas dan status campaign
- Kategori dan section dapat diubah kapan saja

### 2. **Mengatur Tampilan**
- **Featured**: Untuk campaign unggulan
- **Urgent**: Untuk campaign medis/bencana yang butuh cepat
- **Popular**: Untuk campaign yang sudah populer
- **New**: Untuk campaign baru (default)
- **Ending Soon**: Untuk campaign yang hampir berakhir

### 3. **Best Practices**
- Gunakan section "Urgent" untuk campaign medis dan bencana
- Gunakan section "Featured" untuk campaign dengan progress tinggi
- Pindahkan ke "Ending Soon" ketika < 30 hari tersisa
- Pindahkan ke "Popular" ketika sudah banyak donatur

## Implementasi di Frontend

### Query Campaign berdasarkan Section:
```php
// Featured Campaigns
$featuredCampaigns = Campaign::where('section', 'featured')
    ->where('status', 'active')
    ->where('is_verified', true)
    ->take(4)
    ->get();

// Urgent Campaigns
$urgentCampaigns = Campaign::where('section', 'urgent')
    ->where('status', 'active')
    ->where('is_verified', true)
    ->take(3)
    ->get();

// Popular Campaigns
$popularCampaigns = Campaign::where('section', 'popular')
    ->where('status', 'active')
    ->where('is_verified', true)
    ->take(6)
    ->get();

// New Campaigns
$newCampaigns = Campaign::where('section', 'new')
    ->where('status', 'active')
    ->where('is_verified', true)
    ->take(8)
    ->get();

// Ending Soon Campaigns
$endingSoonCampaigns = Campaign::where('section', 'ending_soon')
    ->where('status', 'active')
    ->where('is_verified', true)
    ->take(4)
    ->get();
```

### Query Campaign berdasarkan Kategori:
```php
// Campaign berdasarkan kategori
$medicalCampaigns = Campaign::where('category', 'medical')
    ->where('status', 'active')
    ->where('is_verified', true)
    ->get();

$educationCampaigns = Campaign::where('category', 'education')
    ->where('status', 'active')
    ->where('is_verified', true)
    ->get();
```

## Label dan Warna

### Kategori Labels:
- **Yatim & Dhuafa**: Biru
- **Bantuan Medis**: Hijau
- **Pendidikan**: Merah
- **Masjid**: Kuning
- **Bencana Alam**: Ungu
- **Panti Asuhan**: Emerald
- **Rumah Sakit**: Pink
- **Sekolah**: Cyan
- **Masyarakat**: Orange
- **Lingkungan**: Green
- **Hewan**: Gray
- **Lainnya**: Gray

### Section Labels:
- **Unggulan**: Ungu
- **Urgent**: Merah
- **Populer**: Orange
- **Baru**: Hijau
- **Berakhir Segera**: Kuning

## Migration dan Database

Field yang ditambahkan:
- `section` (enum): Menentukan section tampilan
- `category` (enum): Diperluas dari 4 menjadi 12 kategori

Migration file: `2025_06_25_120517_add_section_to_campaigns_table.php` 