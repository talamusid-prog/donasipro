<?php
echo "<h2>🔧 DIRECT FIX - Mengatasi 404 Storage Files</h2>\n";

// 1. COPY STORAGE FILES LANGSUNG
echo "<h3>1. Copying Storage Files...</h3>\n";
$sourceDir = dirname(__DIR__) . '/storage/app/public/';
$targetDir = __DIR__ . '/storage/';

if (is_dir($sourceDir)) {
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    $categories = ['campaigns', 'categories', 'sliders'];
    $totalCopied = 0;
    
    foreach ($categories as $category) {
        $source = $sourceDir . $category . '/';
        $target = $targetDir . $category . '/';
        
        if (is_dir($source)) {
            if (!is_dir($target)) {
                mkdir($target, 0755, true);
            }
            
            $files = glob($source . '*');
            $copied = 0;
            
            foreach ($files as $file) {
                if (is_file($file)) {
                    $filename = basename($file);
                    $targetFile = $target . $filename;
                    if (copy($file, $targetFile)) {
                        $copied++;
                        $totalCopied++;
                    }
                }
            }
            echo "✅ $category: $copied files copied<br>\n";
        } else {
            echo "❌ Source $category not found<br>\n";
        }
    }
    echo "<strong>Total files copied: $totalCopied</strong><br><br>\n";
} else {
    echo "❌ Source storage directory not found!<br>\n";
}

// 2. TEST FILE ACCESS
echo "<h3>2. Testing File Access...</h3>\n";
$testFiles = [
    'build/assets/app-YOxk1Gow.css',
    'build/assets/app-DaBYqt0m.js'
];

foreach ($testFiles as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        $size = filesize($path);
        echo "✅ $file exists (" . number_format($size/1024, 1) . " KB)<br>\n";
    } else {
        echo "❌ $file NOT FOUND<br>\n";
    }
}

// 3. TEST STORAGE ACCESS
echo "<h3>3. Testing Storage Access...</h3>\n";
$storageCategories = ['campaigns', 'categories', 'sliders'];
foreach ($storageCategories as $category) {
    $dir = __DIR__ . '/storage/' . $category . '/';
    if (is_dir($dir)) {
        $files = glob($dir . '*');
        $count = count($files);
        echo "✅ storage/$category/: $count files<br>\n";
        
        // Test first file
        if ($count > 0) {
            $firstFile = basename($files[0]);
            echo "   Example: /storage/$category/$firstFile<br>\n";
        }
    } else {
        echo "❌ storage/$category/: NOT FOUND<br>\n";
    }
}

// 4. CREATE SIMPLE SYMLINK TEST
echo "<h3>4. Testing Laravel Storage Link...</h3>\n";
$linkTarget = dirname(__DIR__) . '/storage/app/public';
$linkPath = __DIR__ . '/storage-backup';

if (is_link($linkPath)) {
    unlink($linkPath);
}

if (symlink($linkTarget, $linkPath)) {
    echo "✅ Symlink test successful<br>\n";
    unlink($linkPath); // Clean up
} else {
    echo "❌ Symlink not supported on this server<br>\n";
}

echo "<h3>✅ FIXES COMPLETED!</h3>\n";
echo "<p><strong>Silakan refresh website Anda sekarang untuk test.</strong></p>\n";
echo "<p><a href='/' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🏠 Test Website</a></p>\n";
?> 