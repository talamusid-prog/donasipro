<?php
/**
 * Endpoint test untuk cek koneksi ke server NodeJS
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Cek koneksi ke server NodeJS
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$nodeUrl = $protocol . '://' . $host . ':3002';

echo json_encode([
    'success' => true,
    'message' => 'Test endpoint berjalan',
    'node_url' => $nodeUrl,
    'timestamp' => date('Y-m-d H:i:s'),
    'server_info' => [
        'host' => $host,
        'protocol' => $protocol,
        'port' => '3002'
    ]
]);
?> 