<?php
/**
 * Script untuk testing pengiriman pesan WhatsApp langsung
 * 
 * Usage: php test_send_wa.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\WhatsAppService;

try {
    echo "=== TEST SEND WHATSAPP MESSAGE ===\n\n";
    
    // Nomor tujuan
    $phone = '085159205506';
    $message = 'Test pesan dari script PHP - ' . date('Y-m-d H:i:s');
    
    echo "Nomor tujuan: {$phone}\n";
    echo "Pesan: {$message}\n\n";
    
    // Inisialisasi WhatsApp service
    $whatsappService = new WhatsAppService();
    
    // Kirim pesan
    echo "Mengirim pesan...\n";
    $result = $whatsappService->sendNotification($phone, $message);
    
    echo "\n=== HASIL ===\n";
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    
    if ($result['success']) {
        echo "\n✅ Pesan berhasil dikirim!\n";
    } else {
        echo "\n❌ Gagal mengirim pesan: " . ($result['message'] ?? 'Unknown error') . "\n";
    }
    
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

