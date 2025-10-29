<?php
/**
 * Sync Storage Files untuk Hosting
 * File ini akan menyalin semua file dari storage/app/public ke public/storage
 * Jalankan sekali setelah deploy ke hosting
 */

echo "<h1>🔄 Sync Storage Files untuk Hosting</h1>";

$sourceBase = dirname(__DIR__) . '/storage/app/public/';
$targetBase = __DIR__ . '/storage/';

echo "<p>Source: $sourceBase</p>";
echo "<p>Target: $targetBase</p>";

if (!is_dir($sourceBase)) {
    die("<p style='color: red;'>❌ Source directory tidak ditemukan: $sourceBase</p>");
}

// Buat target directory jika belum ada
if (!is_dir($targetBase)) {
    if (mkdir($targetBase, 0755, true)) {
        echo "<p style='color: green;'>✅ Created target directory: $targetBase</p>";
    } else {
        die("<p style='color: red;'>❌ Gagal membuat target directory: $targetBase</p>");
    }
}

function copyDirectory($source, $target) {
    global $sourceBase, $targetBase;
    
    if (!is_dir($source)) {
        return 0;
    }
    
    $copied = 0;
    $files = scandir($source);
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $sourcePath = $source . '/' . $file;
        $targetPath = $target . '/' . $file;
        
        if (is_dir($sourcePath)) {
            // Buat subdirectory
            if (!is_dir($targetPath)) {
                mkdir($targetPath, 0755, true);
            }
            $copied += copyDirectory($sourcePath, $targetPath);
        } else {
            // Copy file
            if (!file_exists($targetPath) || filemtime($sourcePath) > filemtime($targetPath)) {
                if (copy($sourcePath, $targetPath)) {
                    echo "<p style='color: green;'>✅ Copied: " . str_replace($sourceBase, '', $sourcePath) . "</p>";
                    $copied++;
                } else {
                    echo "<p style='color: red;'>❌ Failed to copy: " . str_replace($sourceBase, '', $sourcePath) . "</p>";
                }
            } else {
                echo "<p style='color: blue;'>ℹ️ Already exists: " . str_replace($sourceBase, '', $sourcePath) . "</p>";
            }
        }
    }
    
    return $copied;
}

// Sync semua file
$totalCopied = copyDirectory($sourceBase, $targetBase);

echo "<h2>🎉 Sync Complete!</h2>";
echo "<p><strong>Total files copied: $totalCopied</strong></p>";

// Test akses file
echo "<h2>🧪 Test File Access</h2>";
$testFiles = [
    'payment_proofs/payment_proof_30_1751131077.png',
    'payment_proofs/payment_proof_35_1751131701.jpeg'
];

foreach ($testFiles as $testFile) {
    $testPath = $targetBase . $testFile;
    if (file_exists($testPath)) {
        $size = filesize($testPath);
        echo "<p style='color: green;'>✅ $testFile ($size bytes)</p>";
        echo "<p><a href='/storage/$testFile' target='_blank'>Test Web Access</a> | ";
        echo "<a href='/serve-file.php?file=$testFile' target='_blank'>Test File Server</a></p>";
    } else {
        echo "<p style='color: red;'>❌ $testFile not found</p>";
    }
}

echo "<h2>📋 Next Steps</h2>";
echo "<p>1. Upload file ini ke hosting</p>";
echo "<p>2. Akses: https://juaraapps.my.id/sync-storage-for-hosting.php</p>";
echo "<p>3. Hapus file ini setelah selesai untuk keamanan</p>";
?> 