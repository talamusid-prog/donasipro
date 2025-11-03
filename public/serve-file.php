<?php
/**
 * File Server untuk Hosting yang tidak mendukung symlink dengan baik
 * Akses: /serve-file.php?file=payment_proofs/payment_proof_35_1751131701.jpeg
 */

// Security: hanya izinkan akses ke file tertentu
$allowedExtensions = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'pdf'];
$allowedDirectories = ['payment_proofs', 'campaigns', 'categories', 'sliders', 'bank-logos', 'editor-images'];

// Get file path from query parameter
$filePath = $_GET['file'] ?? '';

// Validate file path
if (empty($filePath)) {
    http_response_code(400);
    die('File parameter is required');
}

// Check if file is in allowed directory
$directory = explode('/', $filePath)[0] ?? '';
if (!in_array($directory, $allowedDirectories)) {
    http_response_code(403);
    die('Access denied to this directory');
}

// Check file extension
$extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
if (!in_array($extension, $allowedExtensions)) {
    http_response_code(403);
    die('File type not allowed');
}

// Build full path - coba beberapa kemungkinan path
$possiblePaths = [
    realpath(__DIR__ . '/../storage/app/public/' . $filePath),
    realpath(__DIR__ . '/storage/' . $filePath),
    realpath(__DIR__ . '/../storage/app/public/' . $filePath),
    __DIR__ . '/../storage/app/public/' . $filePath,
    __DIR__ . '/storage/' . $filePath
];

$fullPath = null;
foreach ($possiblePaths as $path) {
    if ($path && file_exists($path)) {
        $fullPath = $path;
        break;
    }
}

// Check if file exists
if (!$fullPath || !file_exists($fullPath)) {
    http_response_code(404);
    echo "File not found. Tried paths:<br>";
    foreach ($possiblePaths as $i => $path) {
        echo ($i + 1) . ". " . $path . " - " . (file_exists($path) ? "EXISTS" : "NOT FOUND") . "<br>";
    }
    die();
}

// Set appropriate headers
switch($extension) {
    case 'png':
        header('Content-Type: image/png');
        break;
    case 'jpg':
    case 'jpeg':
        header('Content-Type: image/jpeg');
        break;
    case 'gif':
        header('Content-Type: image/gif');
        break;
    case 'webp':
        header('Content-Type: image/webp');
        break;
    case 'pdf':
        header('Content-Type: application/pdf');
        break;
    default:
        header('Content-Type: application/octet-stream');
}

// Set cache headers
header('Cache-Control: public, max-age=31536000'); // 1 year
header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
header('Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T', filemtime($fullPath)));

// Output file
readfile($fullPath);
exit;
?> 