<?php
echo "<h1>🔥 COPY STORAGE FILES - SIMPLE & DIRECT</h1>\n";

$sourceBase = dirname(__DIR__) . '/storage/app/public/';
$targetBase = __DIR__ . '/storage/';

echo "<p><strong>Source:</strong> " . realpath($sourceBase) . "</p>\n";
echo "<p><strong>Target:</strong> " . realpath(__DIR__) . "/storage/</p>\n";

// Create target directory
if (!is_dir($targetBase)) {
    mkdir($targetBase, 0755, true);
    echo "<p>✅ Created target directory</p>\n";
}

$categories = ['campaigns', 'categories', 'sliders'];
$allFiles = [];

foreach ($categories as $category) {
    echo "<h2>📁 Processing $category</h2>\n";
    
    $sourceDir = $sourceBase . $category . '/';
    $targetDir = $targetBase . $category . '/';
    
    echo "<p>From: $sourceDir</p>\n";
    echo "<p>To: $targetDir</p>\n";
    
    if (!is_dir($sourceDir)) {
        echo "<p>❌ Source directory not found: $sourceDir</p>\n";
        continue;
    }
    
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
        echo "<p>✅ Created target subdirectory</p>\n";
    }
    
    $files = scandir($sourceDir);
    $copied = 0;
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $sourceFile = $sourceDir . $file;
        $targetFile = $targetDir . $file;
        
        if (is_file($sourceFile)) {
            if (copy($sourceFile, $targetFile)) {
                echo "<span style='color: green;'>✅ $file</span><br>\n";
                $copied++;
                $allFiles[] = "/storage/$category/$file";
            } else {
                echo "<span style='color: red;'>❌ $file (FAILED)</span><br>\n";
            }
        }
    }
    
    echo "<p><strong>$category: $copied files copied</strong></p>\n";
}

echo "<h2>🎯 TESTING COPIED FILES</h2>\n";
$domain = $_SERVER['HTTP_HOST'];
$protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';

foreach ($allFiles as $file) {
    $localPath = __DIR__ . str_replace('/storage/', '/storage/', $file);
    if (file_exists($localPath)) {
        $size = filesize($localPath);
        echo "<p>✅ <a href='$protocol://$domain$file' target='_blank'>$file</a> (" . number_format($size/1024, 1) . " KB)</p>\n";
    } else {
        echo "<p>❌ $file (NOT FOUND LOCALLY)</p>\n";
    }
}

echo "<h2>📋 QUICK TEST LINKS</h2>\n";
$testFiles = [
    '/storage/campaigns/1750909928_bb31880e-03e9-4b7f-b3b1-dd431d732d44.jpg',
    '/storage/categories/1750858563_icon-1742633119438-701236613.png',
    '/storage/sliders/1750875710_1742635102550-333511374.jpg'
];

foreach ($testFiles as $testFile) {
    echo "<p><a href='$protocol://$domain$testFile' target='_blank' style='color: blue;'>Test: $testFile</a></p>\n";
}

echo "<h2>🎉 DONE!</h2>\n";
echo "<p><strong>Total files processed: " . count($allFiles) . "</strong></p>\n";
echo "<p><a href='/' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🏠 Go to Website</a></p>\n";
?> 