<?php
// Simple test file
echo "<h1>✅ PHP TEST SCRIPT WORKING!</h1>\n";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>\n";
echo "<p>PHP Version: " . PHP_VERSION . "</p>\n";
echo "<p>Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>\n";

// Test file existence
$files = [
    'fix-path-mismatch.php',
    'test-storage-access.php', 
    'clear-cache.php',
    'ultimate-fix.php'
];

echo "<h2>🔍 CHECKING FILES IN PUBLIC DIRECTORY</h2>\n";
foreach ($files as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        $size = filesize(__DIR__ . '/' . $file);
        echo "<p>✅ $file exists (" . number_format($size/1024, 1) . " KB)</p>\n";
    } else {
        echo "<p>❌ $file NOT FOUND</p>\n";
    }
}

// Check .htaccess
echo "<h2>⚙️ CHECKING .HTACCESS</h2>\n";
$htaccessFile = __DIR__ . '/.htaccess';
if (file_exists($htaccessFile)) {
    $content = file_get_contents($htaccessFile);
    echo "<p>✅ .htaccess exists</p>\n";
    echo "<h4>Current .htaccess content:</h4>\n";
    echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; max-height: 300px; overflow-y: auto;'>" . htmlspecialchars($content) . "</pre>\n";
    
    // Check if it has problematic rules
    if (strpos($content, 'RewriteRule ^(.*)$ index.php [L]') !== false) {
        echo "<p>⚠️ .htaccess might be redirecting all requests to index.php</p>\n";
        echo "<p><a href='?fix_htaccess=1' style='background: #ffc107; color: black; padding: 8px 16px; text-decoration: none; border-radius: 4px;'>🔧 Fix .htaccess</a></p>\n";
    }
} else {
    echo "<p>❌ .htaccess not found</p>\n";
}

// Fix .htaccess if requested
if (isset($_GET['fix_htaccess'])) {
    $newHtaccess = 'RewriteEngine On

# Allow direct access to PHP files
RewriteCond %{REQUEST_FILENAME} -f
RewriteRule \.php$ - [L]

# Storage files - direct pass through
RewriteRule ^storage/(.*)$ storage/$1 [L]

# Build assets - direct pass through  
RewriteRule ^build/(.*)$ build/$1 [L]

# Laravel fallback for non-existing files
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [L]

# MIME Types
AddType text/css .css
AddType application/javascript .js
AddType image/png .png
AddType image/jpeg .jpg
AddType image/gif .gif
AddType image/webp .webp
';
    
    if (file_put_contents($htaccessFile, $newHtaccess)) {
        echo "<p>✅ .htaccess updated - PHP files should now be accessible</p>\n";
    } else {
        echo "<p>❌ Failed to update .htaccess</p>\n";
    }
}

echo "<h2>🔗 TEST LINKS</h2>\n";
$protocol = 'https';
$domain = $_SERVER['HTTP_HOST'];

$testUrls = [
    'fix-path-mismatch.php' => 'Fix Path Mismatch',
    'test-storage-access.php' => 'Test Storage Access',
    'clear-cache.php' => 'Clear Cache',
    'ultimate-fix.php' => 'Ultimate Fix'
];

foreach ($testUrls as $file => $label) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "<p><a href='$protocol://$domain/$file' target='_blank' style='background: #007bff; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; margin: 5px;'>$label</a></p>\n";
    }
}

echo "<h2>🏠 WEBSITE TEST</h2>\n";
echo "<p><a href='$protocol://$domain/' target='_blank' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🏠 Test Home Page</a></p>\n";
?> 