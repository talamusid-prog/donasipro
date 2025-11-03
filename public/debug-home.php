<?php
echo "<h1>🔍 DEBUG HOME PAGE ERRORS</h1>\n";

// 1. TEST BUILD ASSETS
echo "<h2>1. 🎨 TESTING BUILD ASSETS</h2>\n";
$buildDir = __DIR__ . '/build/assets/';
$manifest = __DIR__ . '/build/manifest.json';

echo "<p><strong>Build directory:</strong> " . realpath($buildDir) . "</p>\n";
echo "<p><strong>Manifest file:</strong> " . realpath($manifest) . "</p>\n";

if (file_exists($manifest)) {
    $manifestData = json_decode(file_get_contents($manifest), true);
    echo "<h3>📋 Manifest Content:</h3>\n";
    foreach ($manifestData as $src => $info) {
        echo "<p>• $src → {$info['file']}</p>\n";
        
        $filePath = $buildDir . $info['file'];
        if (file_exists($filePath)) {
            $size = filesize($filePath);
            echo "<p>  ✅ File exists (" . number_format($size/1024, 1) . " KB)</p>\n";
        } else {
            echo "<p>  ❌ File NOT FOUND: $filePath</p>\n";
        }
    }
} else {
    echo "<p>❌ Manifest file not found</p>\n";
}

// 2. TEST CSS/JS FILES LANGSUNG
echo "<h2>2. 🧪 TESTING DIRECT CSS/JS ACCESS</h2>\n";
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$domain = $_SERVER['HTTP_HOST'];

$testFiles = [
    '/build/assets/app-YOxk1Gow.css',
    '/build/assets/app-DaBYqt0m.js'
];

foreach ($testFiles as $file) {
    $localPath = __DIR__ . $file;
    if (file_exists($localPath)) {
        $size = filesize($localPath);
        echo "<p>✅ <a href='$protocol://$domain$file' target='_blank'>$file</a> (" . number_format($size/1024, 1) . " KB)</p>\n";
    } else {
        echo "<p>❌ $file NOT FOUND</p>\n";
    }
}

// 3. CHECK LARAVEL ROUTES
echo "<h2>3. 🛣️ TESTING LARAVEL ROUTES</h2>\n";
echo "<p><a href='$protocol://$domain/' target='_blank'>🏠 Home Page</a></p>\n";
echo "<p><a href='$protocol://$domain/campaigns' target='_blank'>📋 Campaigns Page</a></p>\n";

// 4. CHECK .HTACCESS
echo "<h2>4. ⚙️ CHECKING .HTACCESS</h2>\n";
$htaccessFile = __DIR__ . '/.htaccess';
if (file_exists($htaccessFile)) {
    echo "<p>✅ .htaccess exists</p>\n";
    echo "<h4>Content:</h4>\n";
    echo "<pre>" . htmlspecialchars(file_get_contents($htaccessFile)) . "</pre>\n";
} else {
    echo "<p>❌ .htaccess NOT FOUND</p>\n";
}

// 5. CHECK PHP ERRORS
echo "<h2>5. 🐛 PHP ERROR CHECKING</h2>\n";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<p>✅ Error reporting enabled</p>\n";
echo "<p>PHP Version: " . PHP_VERSION . "</p>\n";

// 6. TEST LARAVEL APP
echo "<h2>6. 🚀 TESTING LARAVEL APP</h2>\n";
$laravelIndex = __DIR__ . '/index.php';
if (file_exists($laravelIndex)) {
    echo "<p>✅ Laravel index.php exists</p>\n";
    
    // Try to check if Laravel app loads
    ob_start();
    try {
        // Don't actually include it, just check it's readable
        $content = file_get_contents($laravelIndex, false, null, 0, 500);
        echo "<p>✅ index.php is readable</p>\n";
        echo "<p>First 500 chars: <code>" . htmlspecialchars(substr($content, 0, 200)) . "...</code></p>\n";
    } catch (Exception $e) {
        echo "<p>❌ Error reading index.php: " . $e->getMessage() . "</p>\n";
    }
    ob_end_clean();
} else {
    echo "<p>❌ Laravel index.php NOT FOUND</p>\n";
}

// 7. SERVER INFO
echo "<h2>7. 🖥️ SERVER INFO</h2>\n";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>\n";
echo "<p><strong>Script Name:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>\n";
echo "<p><strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>\n";

echo "<h2>🎯 NEXT STEPS</h2>\n";
echo "<p>1. Klik link test di atas untuk cek asset files</p>\n";
echo "<p>2. Klik Home Page untuk lihat error spesifik</p>\n";
echo "<p>3. Check browser console untuk error messages</p>\n";
echo "<p><a href='/' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🏠 GO TO HOME</a></p>\n";
?> 