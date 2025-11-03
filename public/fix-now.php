<?php
echo "<h1>🚨 EMERGENCY FIX - Mengatasi Semua Masalah 404</h1>\n";

// LANGKAH 1: COPY STORAGE FILES PAKSA
echo "<h2>1. COPY STORAGE FILES (PAKSA)</h2>\n";
$sourceDir = dirname(__DIR__) . '/storage/app/public/';
$targetDir = __DIR__ . '/storage/';

echo "Source: $sourceDir<br>\n";
echo "Target: $targetDir<br><br>\n";

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
    echo "✅ Created target directory<br>\n";
}

$categories = ['campaigns', 'categories', 'sliders'];
$totalFiles = 0;

foreach ($categories as $category) {
    $source = $sourceDir . $category . '/';
    $target = $targetDir . $category . '/';
    
    echo "<h3>Processing $category...</h3>\n";
    
    if (is_dir($source)) {
        if (!is_dir($target)) {
            mkdir($target, 0755, true);
        }
        
        $files = glob($source . '*');
        echo "Found " . count($files) . " files in source<br>\n";
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $filename = basename($file);
                $targetFile = $target . $filename;
                
                if (copy($file, $targetFile)) {
                    echo "✅ Copied: $filename<br>\n";
                    $totalFiles++;
                } else {
                    echo "❌ Failed: $filename<br>\n";
                }
            }
        }
    } else {
        echo "❌ Source directory not found: $source<br>\n";
    }
}

echo "<strong>🎉 Total files copied: $totalFiles</strong><br><br>\n";

// LANGKAH 2: FIX LAYOUT FILE SECARA PAKSA
echo "<h2>2. FIX LAYOUT FILE</h2>\n";
$layoutFile = dirname(__DIR__) . '/resources/views/layouts/app.blade.php';

if (file_exists($layoutFile)) {
    $content = file_get_contents($layoutFile);
    
    // Remove the problematic try-catch block and replace with direct links
    $newContent = str_replace(
        '@if(false)
        {{-- Development mode DISABLED untuk production --}}
        @vite([\'resources/css/app.css\', \'resources/js/app.js\'])
    @else
        {{-- Production mode dengan manifest file --}}
        @try
            @php
                $manifest = json_decode(file_get_contents(public_path(\'build/manifest.json\')), true);
                $cssFile = $manifest[\'resources/css/app.css\'][\'file\'] ?? \'assets/app.css\';
                $jsFile = $manifest[\'resources/js/app.js\'][\'file\'] ?? \'assets/app.js\';
            @endphp
            <link rel="stylesheet" href="{{ asset(\'build/\' . $cssFile) }}">
            <script src="{{ asset(\'build/\' . $jsFile) }}" defer></script>
        @catch(Exception $e)
            {{-- Fallback dengan nama file yang benar dari manifest --}}
            <link rel="stylesheet" href="{{ asset(\'build/assets/app-YOxk1Gow.css\') }}">
            <script src="{{ asset(\'build/assets/app-DaBYqt0m.js\') }}" defer></script>
        @endtry
    @endif',
        '{{-- PRODUCTION MODE FORCED --}}
    <link rel="stylesheet" href="{{ asset(\'build/assets/app-YOxk1Gow.css\') }}">
    <script src="{{ asset(\'build/assets/app-DaBYqt0m.js\') }}" defer></script>',
        $content
    );
    
    if (file_put_contents($layoutFile, $newContent)) {
        echo "✅ Layout file updated successfully<br>\n";
    } else {
        echo "❌ Failed to update layout file<br>\n";
    }
} else {
    echo "❌ Layout file not found<br>\n";
}

// LANGKAH 3: CREATE SUPER SIMPLE HTACCESS
echo "<h2>3. CREATE SIMPLE .HTACCESS</h2>\n";
$htaccessContent = 'RewriteEngine On

# Storage files
RewriteRule ^storage/(.*)$ storage/$1 [L]

# Build assets
RewriteRule ^build/(.*)$ build/$1 [L]

# Laravel
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
';

if (file_put_contents(__DIR__ . '/.htaccess', $htaccessContent)) {
    echo "✅ .htaccess created successfully<br>\n";
} else {
    echo "❌ Failed to create .htaccess<br>\n";
}

// LANGKAH 4: VERIFY FILES
echo "<h2>4. VERIFY FILES</h2>\n";

// Check build assets
$buildAssets = ['app-YOxk1Gow.css', 'app-DaBYqt0m.js'];
foreach ($buildAssets as $asset) {
    $path = __DIR__ . '/build/assets/' . $asset;
    if (file_exists($path)) {
        $size = filesize($path);
        echo "✅ build/assets/$asset (" . number_format($size/1024, 1) . " KB)<br>\n";
    } else {
        echo "❌ build/assets/$asset NOT FOUND<br>\n";
    }
}

// Check storage files
foreach ($categories as $category) {
    $dir = __DIR__ . '/storage/' . $category . '/';
    if (is_dir($dir)) {
        $files = glob($dir . '*');
        echo "✅ storage/$category/: " . count($files) . " files<br>\n";
    } else {
        echo "❌ storage/$category/: NOT FOUND<br>\n";
    }
}

// LANGKAH 5: TEST DIRECT ACCESS
echo "<h2>5. TEST DIRECT ACCESS</h2>\n";
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$domain = $_SERVER['HTTP_HOST'];
$baseUrl = "$protocol://$domain";

$testUrls = [
    '/build/assets/app-YOxk1Gow.css',
    '/build/assets/app-DaBYqt0m.js',
    '/storage/campaigns/',
    '/storage/categories/',
    '/storage/sliders/'
];

foreach ($testUrls as $url) {
    echo "<a href='$baseUrl$url' target='_blank'>$url</a><br>\n";
}

echo "<h2>🎉 ALL FIXES COMPLETED!</h2>\n";
echo "<p><strong>Silakan refresh website Anda sekarang!</strong></p>\n";
echo "<p><a href='/' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 18px;'>🏠 TEST WEBSITE SEKARANG</a></p>\n";
?> 