<?php
/**
 * Test endpoint untuk cek koneksi ke server NodeJS di localhost
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Test koneksi ke server NodeJS
$nodeUrl = 'http://localhost:3001';
$healthUrl = $nodeUrl . '/health';
$statusUrl = $nodeUrl . '/whatsapp/status';

echo json_encode([
    'success' => true,
    'message' => 'Testing koneksi ke NodeJS server',
    'node_url' => $nodeUrl,
    'health_url' => $healthUrl,
    'status_url' => $statusUrl,
    'timestamp' => date('Y-m-d H:i:s'),
    'server_info' => [
        'host' => $_SERVER['HTTP_HOST'],
        'protocol' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http',
        'node_port' => '3001'
    ]
]);

// Test koneksi ke health endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $healthUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "\n\nHealth Check Result:\n";
echo json_encode([
    'health_check' => [
        'url' => $healthUrl,
        'http_code' => $httpCode,
        'response' => $response,
        'error' => $error,
        'success' => $httpCode === 200
    ]
]);

// Test koneksi ke status endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $statusUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "\n\nStatus Check Result:\n";
echo json_encode([
    'status_check' => [
        'url' => $statusUrl,
        'http_code' => $httpCode,
        'response' => $response,
        'error' => $error,
        'success' => $httpCode === 200
    ]
]);
?> 