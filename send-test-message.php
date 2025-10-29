<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST SEND MESSAGE TO 6285159205506 ===\n";

try {
    $whatsappService = app('App\Services\WhatsAppService');
    $result = $whatsappService->sendNotification('6285159205506', 'Test pesan dari aplikasi donasi - ' . date('Y-m-d H:i:s'));
    
    echo "Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
    
    if ($result['success']) {
        echo "✅ PESAN BERHASIL DIKIRIM!\n";
    } else {
        echo "❌ GAGAL KIRIM PESAN: " . $result['message'] . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== COMPLETED ===\n"; 