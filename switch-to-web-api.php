<?php
/**
 * Script untuk beralih ke WhatsApp Web API
 * Menggunakan server WhatsApp Web API yang lebih sederhana
 */

$envFile = '.env';

if (!file_exists($envFile)) {
    echo "❌ File .env tidak ditemukan!\n";
    echo "📝 Buat file .env terlebih dahulu\n";
    exit(1);
}

// Baca file .env
$envContent = file_get_contents($envFile);

// Konfigurasi untuk WhatsApp Web API
$configs = [
    'WHATSAPP_METHOD' => 'web_api',
    'WHATSAPP_NODE_ENABLED' => 'true',
    'WHATSAPP_WEB_API_ENABLED' => 'true',
    'WHATSAPP_NODE_URL' => 'http://localhost:3001',
    'WHATSAPP_WEB_API_URL' => 'http://localhost:3001'
];

$updated = false;

foreach ($configs as $key => $value) {
    // Cek apakah konfigurasi sudah ada
    if (preg_match("/^{$key}=/m", $envContent)) {
        // Update nilai yang ada
        $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
        echo "✅ Updated {$key}={$value}\n";
        $updated = true;
    } else {
        // Tambahkan konfigurasi baru
        $envContent .= "\n{$key}={$value}";
        echo "✅ Added {$key}={$value}\n";
        $updated = true;
    }
}

if ($updated) {
    // Tulis kembali ke file .env
    file_put_contents($envFile, $envContent);
    echo "\n🎉 Konfigurasi WhatsApp Web API berhasil diperbaiki!\n";
    echo "🔗 Server NodeJS: http://localhost:3001\n";
    echo "📱 Method: WhatsApp Web API (Manual QR)\n";
    echo "\n📝 Langkah selanjutnya:\n";
    echo "1. Jalankan server WhatsApp Web API:\n";
    echo "   cd whatsapp-web-api && npm start\n";
    echo "2. Restart aplikasi Laravel\n";
    echo "3. Cek status WhatsApp di admin panel\n";
    echo "4. Generate QR code untuk koneksi\n";
} else {
    echo "ℹ️ Konfigurasi sudah sesuai\n";
}

echo "\n🔧 Konfigurasi saat ini:\n";
foreach ($configs as $key => $value) {
    echo "   {$key}={$value}\n";
}

echo "\n📚 Cara menggunakan WhatsApp Web API:\n";
echo "1. Server akan berjalan di port 3001\n";
echo "2. Generate QR code di admin panel\n";
echo "3. Scan QR code dengan WhatsApp di HP\n";
echo "4. Device akan terhubung secara manual\n";
echo "5. Kirim pesan test untuk memastikan koneksi\n"; 