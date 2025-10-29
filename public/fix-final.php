<?php
echo "=== FIX FINAL SCRIPT - MENGATASI SEMUA 404 ERRORS ===\n\n";

// 1. CEK BUILD ASSETS
echo "1. CHECKING BUILD ASSETS:\n";
$buildDir = __DIR__ . '/build/assets/';
if (is_dir($buildDir)) {
    $files = scandir($buildDir);
    echo "Files in build/assets/:\n";
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $size = filesize($buildDir . $file);
            echo "  - $file (" . number_format($size/1024, 1) . " KB)\n";
        }
    }
} else {
    echo "ERROR: build/assets/ directory tidak ada!\n";
}

// 2. CEK MANIFEST
echo "\n2. CHECKING MANIFEST:\n";
$manifestFile = __DIR__ . '/build/manifest.json';
if (file_exists($manifestFile)) {
    $manifest = json_decode(file_get_contents($manifestFile), true);
    echo "Manifest mapping:\n";
    foreach ($manifest as $src => $info) {
        echo "  $src -> {$info['file']}\n";
    }
} else {
    echo "ERROR: manifest.json tidak ada!\n";
}

// 3. CEK STORAGE FILES
echo "\n3. CHECKING STORAGE FILES:\n";
$storageDir = __DIR__ . '/storage/';
if (is_dir($storageDir)) {
    $categories = ['campaigns', 'categories', 'sliders'];
    foreach ($categories as $category) {
        $dir = $storageDir . $category . '/';
        if (is_dir($dir)) {
            $files = scandir($dir);
            $count = count($files) - 2; // minus . dan ..
            echo "  $category/: $count files\n";
        } else {
            echo "  $category/: TIDAK ADA\n";
        }
    }
} else {
    echo "ERROR: public/storage/ directory tidak ada!\n";
}

// 4. CEK AKSES LANGSUNG
echo "\n4. TESTING DIRECT ACCESS:\n";
$testFiles = [
    'build/assets/app-YOxk1Gow.css',
    'build/assets/app-DaBYqt0m.js',
    'storage/campaigns/',
    'storage/categories/',
    'storage/sliders/'
];

foreach ($testFiles as $file) {
    $path = __DIR__ . '/' . $file;
    if (is_dir($path)) {
        echo "  ✅ Directory $file exists\n";
    } elseif (file_exists($path)) {
        echo "  ✅ File $file exists (" . number_format(filesize($path)/1024, 1) . " KB)\n";
    } else {
        echo "  ❌ $file NOT FOUND\n";
    }
}

// 5. APPLY FIXES
if (isset($_GET['fix'])) {
    echo "\n5. APPLYING FIXES:\n";
    
    // Fix A: Copy storage files jika belum ada
    echo "Fix A: Ensuring storage files...\n";
    $sourceStorage = dirname(__DIR__) . '/storage/app/public/';
    $targetStorage = __DIR__ . '/storage/';
    
    if (is_dir($sourceStorage)) {
        if (!is_dir($targetStorage)) {
            mkdir($targetStorage, 0755, true);
        }
        
        $categories = ['campaigns', 'categories', 'sliders'];
        foreach ($categories as $category) {
            $sourceDir = $sourceStorage . $category . '/';
            $targetDir = $targetStorage . $category . '/';
            
            if (is_dir($sourceDir)) {
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                
                $files = glob($sourceDir . '*');
                $copied = 0;
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $filename = basename($file);
                        $target = $targetDir . $filename;
                        if (!file_exists($target)) {
                            copy($file, $target);
                            $copied++;
                        }
                    }
                }
                echo "  - $category: $copied files copied\n";
            }
        }
    }
    
    // Fix B: Create simple .htaccess for LiteSpeed
    echo "Fix B: Creating simple .htaccess...\n";
    $htaccess = 'RewriteEngine On

# Handle storage files
RewriteRule ^storage/(.*)$ storage/$1 [L]

# Handle build assets  
RewriteRule ^build/(.*)$ build/$1 [L]

# Laravel routes
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L]
';
    file_put_contents(__DIR__ . '/.htaccess', $htaccess);
    echo "  - .htaccess created\n";
    
    // Fix C: Update layout dengan nama file yang benar
    echo "Fix C: Updating layout file...\n";
    $layoutFile = dirname(__DIR__) . '/resources/views/layouts/app.blade.php';
    if (file_exists($layoutFile)) {
        $content = file_get_contents($layoutFile);
        
        // Replace fallback CSS/JS dengan nama file yang benar dari manifest
        $content = str_replace(
            "<link rel=\"stylesheet\" href=\"{{ asset('build/assets/app.css') }}\">",
            "<link rel=\"stylesheet\" href=\"{{ asset('build/assets/app-YOxk1Gow.css') }}\">",
            $content
        );
        $content = str_replace(
            "<script src=\"{{ asset('build/assets/app.js') }}\" defer></script>",
            "<script src=\"{{ asset('build/assets/app-DaBYqt0m.js') }}\" defer></script>",
            $content
        );
        
        file_put_contents($layoutFile, $content);
        echo "  - Layout updated with correct file names\n";
    }
    
    echo "\n✅ ALL FIXES APPLIED!\n";
    echo "Please refresh your website to test.\n";
}

echo "\n=== ACTIONS ===\n";
if (!isset($_GET['fix'])) {
    echo "<a href='?fix=1' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔧 APPLY ALL FIXES</a>\n";
}
echo "<br><br><a href='?' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔄 REFRESH CHECK</a>\n";
?> 