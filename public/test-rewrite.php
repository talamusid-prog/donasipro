<?php
/**
 * Test Rewrite Rules & Fix Script
 * Akses via: https://juaraapps.my.id/test-rewrite.php
 */

echo "<h2>🔧 Rewrite Rules Test & Fix</h2>";

// Test direct file access
echo "<h3>📂 Direct File Access Test:</h3>";
$testFiles = [
    'storage/categories/1750858537_icon-1742633587651-503248045.png',
    'storage/campaigns/1751022642_1738827561whatsapp_image_2025_02_05_at_9_46_29_am_9_jpeg.webp',
    'build/assets/app.css'
];

foreach ($testFiles as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        echo "✅ {$file} EXISTS (Size: " . filesize($fullPath) . " bytes)<br>";
        echo "   🔗 <a href='/{$file}' target='_blank'>Direct link: /{$file}</a><br>";
    } else {
        echo "❌ {$file} NOT FOUND<br>";
    }
    echo "<br>";
}

echo "<h3>⚙️ Server & Config Info:</h3>";
echo "<ul>";
echo "<li><strong>Server:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</li>";
echo "<li><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</li>";
echo "<li><strong>Request URI:</strong> " . $_SERVER['REQUEST_URI'] . "</li>";
echo "<li><strong>Rewrite Module:</strong> " . (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules()) ? 'Available' : 'Unknown') . "</li>";
echo "</ul>";

// Check .htaccess files
echo "<h3>📄 .htaccess Files Check:</h3>";
$htaccessFiles = [
    '../.htaccess' => 'Root .htaccess',
    '.htaccess' => 'Public .htaccess'
];

foreach ($htaccessFiles as $file => $desc) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        echo "✅ {$desc}: EXISTS (Size: " . filesize($fullPath) . " bytes)<br>";
        echo "   📝 <a href='#' onclick=\"showContent('{$file}')\">Show content</a><br>";
    } else {
        echo "❌ {$desc}: NOT FOUND<br>";
    }
}

echo "<h3>🔧 Quick Fix Solutions:</h3>";
echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 5px;'>";
echo "<h4>Option 1: Try Simple .htaccess (Recommended)</h4>";
echo "<p>Replace current .htaccess with simple version that works with LiteSpeed:</p>";
echo "<button onclick='applySimplerHtaccess()'>Apply Simple .htaccess</button>";
echo "</div>";

echo "<div style='background: #fff8f0; padding: 15px; border-radius: 5px; margin-top: 10px;'>";
echo "<h4>Option 2: Direct File Serving (Fallback)</h4>";
echo "<p>Create direct file serving script if .htaccess doesn't work:</p>";
echo "<button onclick='createFileServer()'>Create File Server</button>";
echo "</div>";

?>

<script>
function applySimplerHtaccess() {
    if (confirm('Replace current .htaccess with simpler version?')) {
        fetch('?action=simple_htaccess', {method: 'POST'})
        .then(response => response.text())
        .then(data => {
            alert(data);
            location.reload();
        });
    }
}

function createFileServer() {
    if (confirm('Create file server script?')) {
        fetch('?action=file_server', {method: 'POST'})
        .then(response => response.text())
        .then(data => {
            alert(data);
        });
    }
}
</script>

<?php
// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'simple_htaccess':
            // Create simpler .htaccess for LiteSpeed
            $simpleHtaccess = '# Simple LiteSpeed Configuration
AddHandler application/x-httpd-php82 .php

# Basic rewrite rules
RewriteEngine On

# Handle storage files directly
RewriteCond %{REQUEST_URI} ^/storage/(.*)$
RewriteCond %{DOCUMENT_ROOT}/storage/$1 -f
RewriteRule ^storage/(.*)$ /storage/$1 [L]

# Handle build assets directly  
RewriteCond %{REQUEST_URI} ^/build/(.*)$
RewriteCond %{DOCUMENT_ROOT}/build/$1 -f
RewriteRule ^build/(.*)$ /build/$1 [L]

# Redirect everything else to Laravel
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [L]

# Security
<Files ".env">
    Deny from all
</Files>

Options -Indexes
DirectoryIndex index.php';

            file_put_contents(__DIR__ . '/.htaccess', $simpleHtaccess);
            echo "✅ Simple .htaccess applied! Refresh website to test.";
            exit;
            
        case 'file_server':
            // Create file server script
            $fileServer = '<?php
// Simple file server for storage files
$uri = $_SERVER["REQUEST_URI"];

if (strpos($uri, "/storage/") === 0) {
    $file = __DIR__ . $uri;
    if (file_exists($file)) {
        $mime = mime_content_type($file);
        header("Content-Type: " . $mime);
        header("Content-Length: " . filesize($file));
        readfile($file);
        exit;
    }
}

// Continue to normal Laravel routing
include "index.php";
?>';
            file_put_contents(__DIR__ . '/serve.php', $fileServer);
            echo "✅ File server created! You may need to configure your hosting to use serve.php instead of index.php";
            exit;
    }
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h2, h3, h4 { color: #333; }
ul { background: #f5f5f5; padding: 15px; border-radius: 5px; }
button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
button:hover { background: #005a87; }
a { color: #0066cc; }
</style> 