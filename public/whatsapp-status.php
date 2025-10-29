<?php
/**
 * Endpoint sederhana untuk cek status WhatsApp
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Cek apakah server NodeJS berjalan
function checkNodeServer($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode === 200;
}

// Cek status WhatsApp
// Gunakan WhatsApp Web API di port 3001
$nodeUrl = 'http://localhost:3001';
$healthUrl = $nodeUrl . '/health';
$statusUrl = $nodeUrl . '/whatsapp/status';

$isNodeRunning = checkNodeServer($healthUrl);

if ($isNodeRunning) {
    // Coba ambil status dari NodeJS server
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $statusUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $response) {
        $data = json_decode($response, true);
        echo json_encode([
            'success' => true,
            'data' => $data,
            'source' => 'nodejs'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'data' => [
                'connected' => false,
                'status' => 'disconnected',
                'message' => 'NodeJS server berjalan tapi tidak bisa ambil status'
            ],
            'source' => 'fallback'
        ]);
    }
} else {
    // NodeJS server tidak berjalan, return status default
    echo json_encode([
        'success' => true,
        'data' => [
            'connected' => false,
            'status' => 'disconnected',
            'message' => 'NodeJS server tidak berjalan di port 3001',
            'method' => 'WhatsApp Web API'
        ],
        'source' => 'default'
    ]);
}
?> 