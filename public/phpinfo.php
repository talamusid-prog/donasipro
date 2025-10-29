<?php
// File untuk mengecek konfigurasi PHP
// Akses melalui: https://juaraapps.my.id/phpinfo.php
// HAPUS file ini setelah selesai debugging

echo "<h1>PHP Info - juaraapps.my.id</h1>";
echo "<h2>PHP Version: " . phpversion() . "</h2>";
echo "<h2>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</h2>";

// Check Laravel requirements
echo "<h3>Laravel Requirements Check:</h3>";
echo "<ul>";
echo "<li>PHP >= 8.1: " . (version_compare(PHP_VERSION, '8.1.0', '>=') ? '✅ PASS' : '❌ FAIL') . "</li>";
echo "<li>BCMath: " . (extension_loaded('bcmath') ? '✅ PASS' : '❌ FAIL') . "</li>";
echo "<li>Ctype: " . (extension_loaded('ctype') ? '✅ PASS' : '❌ FAIL') . "</li>";
echo "<li>JSON: " . (extension_loaded('json') ? '✅ PASS' : '❌ FAIL') . "</li>";
echo "<li>Mbstring: " . (extension_loaded('mbstring') ? '✅ PASS' : '❌ FAIL') . "</li>";
echo "<li>OpenSSL: " . (extension_loaded('openssl') ? '✅ PASS' : '❌ FAIL') . "</li>";
echo "<li>PDO: " . (extension_loaded('pdo') ? '✅ PASS' : '❌ FAIL') . "</li>";
echo "<li>Tokenizer: " . (extension_loaded('tokenizer') ? '✅ PASS' : '❌ FAIL') . "</li>";
echo "<li>XML: " . (extension_loaded('xml') ? '✅ PASS' : '❌ FAIL') . "</li>";
echo "</ul>";

// Check file permissions
echo "<h3>File Permissions Check:</h3>";
echo "<ul>";
echo "<li>storage/ writable: " . (is_writable('../storage') ? '✅ PASS' : '❌ FAIL') . "</li>";
echo "<li>bootstrap/cache/ writable: " . (is_writable('../bootstrap/cache') ? '✅ PASS' : '❌ FAIL') . "</li>";
echo "<li>.env exists: " . (file_exists('../.env') ? '✅ PASS' : '❌ FAIL') . "</li>";
echo "</ul>";

// Check current directory
echo "<h3>Current Directory: " . getcwd() . "</h3>";

// Show error log if possible
echo "<h3>Error Log (last 10 lines):</h3>";
$error_log = ini_get('error_log');
if ($error_log && file_exists($error_log)) {
    $lines = file($error_log);
    $last_lines = array_slice($lines, -10);
    echo "<pre>";
    foreach ($last_lines as $line) {
        echo htmlspecialchars($line);
    }
    echo "</pre>";
} else {
    echo "<p>Error log not found or not accessible</p>";
}

// Show loaded extensions
echo "<h3>Loaded Extensions:</h3>";
echo "<pre>";
print_r(get_loaded_extensions());
echo "</pre>";
?> 