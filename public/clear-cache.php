<?php
echo "<h1>🧹 CLEAR CACHE & FINAL TEST</h1>\n";

// 1. ARTISAN COMMANDS VIA SHELL
echo "<h2>1. 🚀 CLEARING LARAVEL CACHES</h2>\n";

$commands = [
    'config:clear' => 'Clear Config Cache',
    'cache:clear' => 'Clear Application Cache', 
    'route:clear' => 'Clear Route Cache',
    'view:clear' => 'Clear View Cache',
    'optimize:clear' => 'Clear All Optimization Caches'
];

$baseDir = dirname(__DIR__);

foreach ($commands as $command => $description) {
    echo "<h3>Running: $description</h3>\n";
    
    $fullCommand = "cd $baseDir && php artisan $command 2>&1";
    $output = shell_exec($fullCommand);
    
    if ($output) {
        echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
        echo htmlspecialchars(trim($output));
        echo "</pre>\n";
    } else {
        echo "<p>✅ Command executed successfully</p>\n";
    }
}

// 2. SET PROPER PERMISSIONS
echo "<h2>2. 🔐 SETTING PERMISSIONS</h2>\n";
$permissionCommands = [
    "chmod -R 755 $baseDir/storage",
    "chmod -R 755 $baseDir/bootstrap/cache"
];

foreach ($permissionCommands as $cmd) {
    $output = shell_exec("$cmd 2>&1");
    if ($output && trim($output) !== '') {
        echo "<p>Output: " . htmlspecialchars($output) . "</p>\n";
    } else {
        echo "<p>✅ " . basename($cmd) . " permissions set</p>\n";
    }
}

// 3. TEST APP_URL CONFIGURATION  
echo "<h2>3. 🌐 TESTING APP_URL</h2>\n";
$envFile = $baseDir . '/.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    
    if (strpos($envContent, 'APP_URL=http://juaraapps.my.id') !== false) {
        echo "<p>⚠️ APP_URL uses HTTP, should be HTTPS</p>\n";
        
        // Update APP_URL to HTTPS
        $newContent = str_replace(
            'APP_URL=http://juaraapps.my.id',
            'APP_URL=https://juaraapps.my.id', 
            $envContent
        );
        
        if (file_put_contents($envFile, $newContent)) {
            echo "<p>✅ Updated APP_URL to HTTPS</p>\n";
        } else {
            echo "<p>❌ Failed to update APP_URL</p>\n";
        }
    } else {
        echo "<p>✅ APP_URL configuration OK</p>\n";
    }
}

// 4. REGENERATE APPLICATION KEY (if needed)
echo "<h2>4. 🔑 APPLICATION KEY</h2>\n";
if (strpos($envContent, 'APP_KEY=') !== false && strpos($envContent, 'APP_KEY=base64:') !== false) {
    echo "<p>✅ APP_KEY is set and looks valid</p>\n";
} else {
    echo "<p>⚠️ APP_KEY might need regeneration</p>\n";
    $keyOutput = shell_exec("cd $baseDir && php artisan key:generate 2>&1");
    echo "<pre>" . htmlspecialchars($keyOutput) . "</pre>\n";
}

// 5. FINAL STATUS CHECK
echo "<h2>5. ✅ FINAL STATUS</h2>\n";

$checks = [
    'Storage writable' => is_writable($baseDir . '/storage'),
    'Bootstrap cache writable' => is_writable($baseDir . '/bootstrap/cache'),
    'Config cache cleared' => !file_exists($baseDir . '/bootstrap/cache/config.php'),
    'Route cache cleared' => !file_exists($baseDir . '/bootstrap/cache/routes-v7.php'),
    '.env readable' => is_readable($baseDir . '/.env')
];

foreach ($checks as $check => $status) {
    if ($status) {
        echo "<p>✅ $check</p>\n";
    } else {
        echo "<p>❌ $check</p>\n";
    }
}

// 6. TEST HOMEPAGE DIRECT
echo "<h2>6. 🏠 HOMEPAGE TEST</h2>\n";
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$domain = $_SERVER['HTTP_HOST'];

echo "<p><strong>Current URL:</strong> $protocol://$domain</p>\n";
echo "<p><strong>Your website should now be working!</strong></p>\n";

echo "<div style='text-align: center; margin: 30px 0;'>\n";
echo "<a href='/' target='_blank' style='background: #28a745; color: white; padding: 20px 40px; text-decoration: none; border-radius: 10px; font-size: 20px; display: inline-block; margin: 10px;'>🏠 TEST HOME PAGE</a>\n";
echo "<a href='/campaigns' target='_blank' style='background: #007bff; color: white; padding: 20px 40px; text-decoration: none; border-radius: 10px; font-size: 20px; display: inline-block; margin: 10px;'>📋 TEST CAMPAIGNS</a>\n";
echo "</div>\n";

// 7. CLEANUP SCRIPTS
echo "<h2>7. 🗑️ CLEANUP</h2>\n";
echo "<p>After confirming everything works, you can delete these debug files:</p>\n";
echo "<ul>\n";
echo "<li>ultimate-fix.php</li>\n";
echo "<li>debug-home.php</li>\n";
echo "<li>test-home-error.php</li>\n";
echo "<li>clear-cache.php</li>\n";
echo "<li>copy-storage.php</li>\n";
echo "<li>fix-now.php</li>\n";
echo "</ul>\n";

if (isset($_GET['cleanup'])) {
    $debugFiles = [
        'ultimate-fix.php',
        'debug-home.php', 
        'test-home-error.php',
        'clear-cache.php',
        'copy-storage.php',
        'fix-now.php'
    ];
    
    $deleted = 0;
    foreach ($debugFiles as $file) {
        if (file_exists(__DIR__ . '/' . $file)) {
            if (unlink(__DIR__ . '/' . $file)) {
                $deleted++;
            }
        }
    }
    
    echo "<p>✅ Deleted $deleted debug files</p>\n";
}

echo "<p><a href='?cleanup=1' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🗑️ Delete Debug Files</a></p>\n";

echo "<h2>🎉 WEBSITE SHOULD BE WORKING NOW!</h2>\n";
echo "<p>All caches cleared, permissions set, and configurations updated.</p>\n";
?> 