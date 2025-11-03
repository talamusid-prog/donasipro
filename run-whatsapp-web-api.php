<?php
/**
 * Script untuk menjalankan server WhatsApp Web API
 */

echo "🚀 Memulai server WhatsApp Web API...\n";
echo "📱 Method: WhatsApp Web API (Manual QR)\n";
echo "🔗 Port: 3001\n";
echo "💡 Tidak memerlukan token Meta\n\n";

// Cek apakah direktori whatsapp-web-api ada
if (!is_dir('whatsapp-web-api')) {
    echo "❌ Direktori whatsapp-web-api tidak ditemukan!\n";
    echo "📝 Pastikan direktori whatsapp-web-api ada di root project\n";
    exit(1);
}

// Cek apakah package.json ada
if (!file_exists('whatsapp-web-api/package.json')) {
    echo "❌ File package.json tidak ditemukan di whatsapp-web-api!\n";
    echo "📝 Pastikan direktori whatsapp-web-api sudah benar\n";
    exit(1);
}

echo "✅ Direktori whatsapp-web-api ditemukan\n";
echo "✅ File package.json ditemukan\n\n";

echo "📋 Langkah untuk menjalankan server:\n";
echo "1. Buka terminal/SSH\n";
echo "2. Masuk ke direktori whatsapp-web-api:\n";
echo "   cd whatsapp-web-api\n";
echo "3. Install dependencies (jika belum):\n";
echo "   npm install\n";
echo "4. Jalankan server:\n";
echo "   npm start\n\n";

echo "🔧 Konfigurasi server:\n";
echo "- Port: 3001\n";
echo "- Method: WhatsApp Web API\n";
echo "- QR Code: Manual scan\n";
echo "- Token: Tidak diperlukan\n";
echo "- Logger: Simple console logger\n\n";

echo "📱 Cara menggunakan:\n";
echo "1. Server akan berjalan di http://localhost:3001\n";
echo "2. Buka admin panel WhatsApp device\n";
echo "3. Klik 'Generate QR Code'\n";
echo "4. Scan QR code dengan WhatsApp di HP\n";
echo "5. Device akan terhubung secara manual\n\n";

echo "💡 Keuntungan WhatsApp Web API:\n";
echo "✅ Gratis tanpa batasan\n";
echo "✅ Tidak perlu token Meta\n";
echo "✅ Mudah setup untuk produksi\n";
echo "✅ Manual QR scan\n";
echo "✅ Cocok untuk shared hosting\n\n";

echo "⚠️  Catatan:\n";
echo "- Pastikan port 3001 tidak digunakan aplikasi lain\n";
echo "- Server harus berjalan terus untuk koneksi WhatsApp\n";
echo "- Jika server restart, perlu scan QR code ulang\n";
echo "- Cocok untuk aplikasi produksi tanpa ketergantungan Meta\n";

echo "\n🎯 Status yang diharapkan:\n";
echo "[INFO] WhatsApp Web API server running on port 3001\n";
echo "[INFO] Health check: http://localhost:3001/health\n";
echo "[INFO] WhatsApp status: http://localhost:3001/whatsapp/status\n";
?> 