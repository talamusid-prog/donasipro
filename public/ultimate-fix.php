<?php
echo "<h1>🚀 ULTIMATE FIX - Mengatasi SEMUA Storage 404 Errors</h1>\n";

// 1. SCAN SEMUA DIREKTORI STORAGE
echo "<h2>1. 🔍 SCANNING STORAGE DIRECTORIES</h2>\n";
$sourceBase = dirname(__DIR__) . '/storage/app/public/';
$targetBase = __DIR__ . '/storage/';

echo "<p><strong>Source Base:</strong> $sourceBase</p>\n";
echo "<p><strong>Target Base:</strong> $targetBase</p>\n";

if (!is_dir($sourceBase)) {
    echo "<p style='color: red;'>❌ ERROR: Source storage directory tidak ditemukan!</p>\n";
    echo "<p>Path: $sourceBase</p>\n";
    exit;
}

// Scan semua subdirectories di storage/app/public
$allDirs = [];
$iterator = new DirectoryIterator($sourceBase);
foreach ($iterator as $item) {
    if ($item->isDot() || !$item->isDir()) continue;
    $allDirs[] = $item->getFilename();
}

echo "<h3>📁 Found directories in storage/app/public:</h3>\n";
foreach ($allDirs as $dir) {
    $files = glob($sourceBase . $dir . '/*');
    $fileCount = count($files);
    echo "<p>• <strong>$dir/</strong> ($fileCount files)</p>\n";
}

// 2. CREATE TARGET DIRECTORIES DAN COPY FILES
echo "<h2>2. 📂 CREATING TARGET DIRECTORIES & COPYING FILES</h2>\n";

if (!is_dir($targetBase)) {
    mkdir($targetBase, 0755, true);
    echo "<p>✅ Created main target directory</p>\n";
}

$totalCopied = 0;
$copiedFiles = [];

foreach ($allDirs as $dir) {
    echo "<h3>Processing: $dir</h3>\n";
    
    $sourceDir = $sourceBase . $dir . '/';
    $targetDir = $targetBase . $dir . '/';
    
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
        echo "<p>✅ Created directory: storage/$dir/</p>\n";
    }
    
    $files = glob($sourceDir . '*');
    $dirCopied = 0;
    
    foreach ($files as $file) {
        if (is_file($file)) {
            $filename = basename($file);
            $targetFile = $targetDir . $filename;
            
            if (copy($file, $targetFile)) {
                echo "<span style='color: green;'>✅ $filename</span><br>\n";
                $dirCopied++;
                $totalCopied++;
                $copiedFiles[] = "/storage/$dir/$filename";
            } else {
                echo "<span style='color: red;'>❌ $filename (FAILED)</span><br>\n";
            }
        }
    }
    
    echo "<p><strong>$dir: $dirCopied files copied</strong></p>\n";
}

echo "<h2>📊 SUMMARY</h2>\n";
echo "<p><strong>Total files copied: $totalCopied</strong></p>\n";
echo "<p><strong>Total directories: " . count($allDirs) . "</strong></p>\n";

// 3. TEST FILE ACCESS
echo "<h2>3. 🧪 TESTING FILE ACCESS</h2>\n";
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$domain = $_SERVER['HTTP_HOST'];

// Test specific files yang error
$errorFiles = [
    '/storage/campaigns/1750909914_landing-page-design-RUMSHOL-OPSI-4-01-650x350.png',
    '/storage/editor-images/1751022411_landing-page-design-RUMSHOL-OPSI-4-01-650x350.png',
    '/storage/campaigns/1750909928_bb31880e-03e9-4b7f-b3b1-dd431d732d44.jpg'
];

echo "<h3>🎯 Testing specific error files:</h3>\n";
foreach ($errorFiles as $file) {
    $localPath = __DIR__ . $file;
    if (file_exists($localPath)) {
        $size = filesize($localPath);
        echo "<p>✅ <a href='$protocol://$domain$file' target='_blank'>$file</a> (" . number_format($size/1024, 1) . " KB) - EXISTS</p>\n";
    } else {
        echo "<p>❌ <strong>$file</strong> - NOT FOUND LOCALLY</p>\n";
        // Try to find it in other directories
        $filename = basename($file);
        foreach ($allDirs as $dir) {
            $searchPath = $targetBase . $dir . '/' . $filename;
            if (file_exists($searchPath)) {
                echo "<p>   → Found in: storage/$dir/$filename</p>\n";
            }
        }
    }
}

// 4. CREATE .HTACCESS RULES
echo "<h2>4. ⚙️ UPDATING .HTACCESS</h2>\n";
$htaccessContent = 'RewriteEngine On

# Storage files - SEMUA subdirectories
RewriteRule ^storage/(.*)$ storage/$1 [L]

# Build assets
RewriteRule ^build/(.*)$ build/$1 [L]

# Laravel routes
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L]

# MIME Types
AddType text/css .css
AddType application/javascript .js
AddType image/png .png
AddType image/jpeg .jpg
AddType image/gif .gif
AddType image/webp .webp
AddType image/svg+xml .svg
';

if (file_put_contents(__DIR__ . '/.htaccess', $htaccessContent)) {
    echo "<p>✅ .htaccess updated successfully</p>\n";
} else {
    echo "<p>❌ Failed to update .htaccess</p>\n";
}

// 5. DIRECTORY LISTING TEST
echo "<h2>5. 📋 DIRECTORY STRUCTURE</h2>\n";
echo "<h3>Current public/storage/ structure:</h3>\n";
if (is_dir($targetBase)) {
    $dirs = scandir($targetBase);
    foreach ($dirs as $dir) {
        if ($dir === '.' || $dir === '..') continue;
        $dirPath = $targetBase . $dir;
        if (is_dir($dirPath)) {
            $fileCount = count(glob($dirPath . '/*')) ;
            echo "<p>📁 <strong>storage/$dir/</strong> ($fileCount files)</p>\n";
            
            // Show first few files as examples
            $files = array_slice(glob($dirPath . '/*'), 0, 3);
            foreach ($files as $file) {
                if (is_file($file)) {
                    $filename = basename($file);
                    echo "<p>   → <a href='$protocol://$domain/storage/$dir/$filename' target='_blank'>$filename</a></p>\n";
                }
            }
        }
    }
} else {
    echo "<p>❌ public/storage/ directory doesn't exist</p>\n";
}

echo "<h2>🎉 ULTIMATE FIX COMPLETED!</h2>\n";
echo "<p><strong>All storage directories have been processed.</strong></p>\n";
echo "<p><a href='/' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 18px;'>🏠 TEST WEBSITE NOW</a></p>\n";

// Clean up
echo "<p><small>You can delete this file after verification: copy-storage.php, fix-now.php, ultimate-fix.php</small></p>\n";
?> 