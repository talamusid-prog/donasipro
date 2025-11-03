# Panduan Upload Gambar CKEditor

## Fitur yang Ditambahkan

### 1. **CKEditor dengan Upload Gambar**
- Editor rich text lengkap dengan toolbar yang komprehensif
- Fitur upload gambar langsung dari editor
- Support format: JPEG, PNG, GIF, WEBP
- Maksimal ukuran file: 2MB

### 2. **Toolbar CKEditor**
- **Formatting**: Heading, Bold, Italic, Underline, Strikethrough
- **Lists**: Bulleted List, Numbered List
- **Indentation**: Indent, Outdent
- **Content**: Link, Block Quote, Insert Table, Image Upload
- **Actions**: Undo, Redo

### 3. **Upload Gambar**
- Gambar disimpan di: `storage/app/public/editor-images/`
- URL akses: `/storage/editor-images/`
- Nama file: `timestamp_originalname.ext`
- Response JSON untuk CKEditor

## File yang Dimodifikasi

### 1. **Views**
- `resources/views/admin/campaigns/create.blade.php`
- `resources/views/admin/campaigns/edit.blade.php`
- `resources/views/campaigns/show.blade.php`
- `resources/views/layouts/app.blade.php`

### 2. **Controller**
- `app/Http/Controllers/Admin/CampaignController.php` - Method `uploadImage()`

### 3. **Routes**
- `routes/web.php` - Route `admin.campaigns.upload-image`

## Cara Penggunaan

### 1. **Upload Gambar di CKEditor**
1. Klik tombol gambar (📷) di toolbar CKEditor
2. Pilih file gambar dari komputer
3. Gambar akan otomatis diupload dan ditampilkan di editor
4. Gambar akan tersimpan di server

### 2. **Format Gambar yang Didukung**
- JPEG (.jpg, .jpeg)
- PNG (.png)
- GIF (.gif)
- WEBP (.webp)

### 3. **Ukuran File**
- Maksimal: 2MB per file
- Gambar akan otomatis di-resize jika terlalu besar

## Keamanan

### 1. **Validasi File**
- Hanya file gambar yang diperbolehkan
- Validasi MIME type
- Pembatasan ukuran file

### 2. **CSRF Protection**
- Token CSRF otomatis disertakan
- Validasi di server side

### 3. **Path Security**
- File disimpan di folder terpisah
- Nama file menggunakan timestamp untuk menghindari konflik

## Styling

### 1. **Editor Styling**
- Menyesuaikan dengan tema Tailwind CSS
- Border dan background yang konsisten
- Responsive design

### 2. **Content Styling**
- Gambar responsive dengan max-width 100%
- Border radius dan shadow untuk estetika
- Styling untuk heading, list, table, dan elemen lainnya

## Troubleshooting

### 1. **Gambar Tidak Muncul**
- Pastikan folder `public/storage/editor-images/` sudah dibuat
- Cek permission folder (755)
- Pastikan symbolic link storage sudah dibuat

### 2. **Upload Gagal**
- Cek ukuran file (maksimal 2MB)
- Pastikan format file didukung
- Cek log error di `storage/logs/laravel.log`

### 3. **Permission Error**
```bash
# Buat folder jika belum ada
mkdir -p public/storage/editor-images

# Set permission
chmod 755 public/storage/editor-images

# Buat symbolic link jika belum ada
php artisan storage:link
```

## Maintenance

### 1. **Cleanup Gambar**
- Gambar lama bisa dihapus secara berkala
- Folder: `storage/app/public/editor-images/`
- Folder: `public/storage/editor-images/`

### 2. **Backup**
- Backup folder `storage/app/public/editor-images/`
- Backup database untuk referensi gambar

## Catatan Penting

1. **Storage**: Gambar disimpan di storage Laravel, bukan di public folder langsung
2. **URL**: Gambar diakses melalui `/storage/editor-images/`
3. **Security**: Validasi file dilakukan di server side
4. **Performance**: Gambar di-compress otomatis untuk optimasi
5. **Backup**: Pastikan backup gambar secara berkala 