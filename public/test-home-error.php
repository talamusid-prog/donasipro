<?php
echo "<h1>🏠 TEST HOME PAGE ERROR</h1>\n";

// 1. TEST LARAVEL BOOTSTRAP
echo "<h2>1. 🚀 TESTING LARAVEL BOOTSTRAP</h2>\n";

try {
    // Check if we can load Laravel bootstrap
    $autoload = dirname(__DIR__) . '/vendor/autoload.php';
    if (file_exists($autoload)) {
        echo "<p>✅ Composer autoload exists</p>\n";
        require $autoload;
        echo "<p>✅ Autoload loaded successfully</p>\n";
    } else {
        echo "<p>❌ Composer autoload NOT FOUND: $autoload</p>\n";
    }
    
    $appFile = dirname(__DIR__) . '/bootstrap/app.php';
    if (file_exists($appFile)) {
        echo "<p>✅ Bootstrap app.php exists</p>\n";
        $app = require $appFile;
        echo "<p>✅ Laravel app bootstrapped</p>\n";
    } else {
        echo "<p>❌ Bootstrap app.php NOT FOUND: $appFile</p>\n";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Laravel Bootstrap Error: " . $e->getMessage() . "</p>\n";
}

// 2. TEST .ENV FILE
echo "<h2>2. ⚙️ TESTING .ENV FILE</h2>\n";
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    echo "<p>✅ .env file exists</p>\n";
    
    $envContent = file_get_contents($envFile);
    $lines = explode("\n", $envContent);
    
    echo "<h4>Key .env variables:</h4>\n";
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        
        if (strpos($line, 'APP_') === 0 || 
            strpos($line, 'DB_') === 0 || 
            strpos($line, 'APP_URL') !== false) {
            echo "<p>• " . htmlspecialchars($line) . "</p>\n";
        }
    }
} else {
    echo "<p>❌ .env file NOT FOUND</p>\n";
}

// 3. TEST DATABASE CONNECTION
echo "<h2>3. 🗄️ TESTING DATABASE</h2>\n";
try {
    if (isset($app)) {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        
        // Get DB config
        $config = $app['config'];
        $dbConnection = $config->get('database.default');
        $dbConfig = $config->get("database.connections.$dbConnection");
        
        echo "<p>✅ Database config loaded</p>\n";
        echo "<p>• Connection: $dbConnection</p>\n";
        echo "<p>• Host: " . ($dbConfig['host'] ?? 'N/A') . "</p>\n";
        echo "<p>• Database: " . ($dbConfig['database'] ?? 'N/A') . "</p>\n";
        
        // Test connection
        $pdo = $app['db']->connection()->getPdo();
        echo "<p>✅ Database connection successful</p>\n";
        
    } else {
        echo "<p>❌ Laravel app not available for DB test</p>\n";
    }
} catch (Exception $e) {
    echo "<p>❌ Database Error: " . $e->getMessage() . "</p>\n";
}

// 4. TEST SPECIFIC ROUTE
echo "<h2>4. 🛣️ TESTING HOME ROUTE</h2>\n";
try {
    if (isset($app)) {
        $request = \Illuminate\Http\Request::create('/', 'GET');
        $response = $app->handle($request);
        
        echo "<p>✅ Home route response: " . $response->getStatusCode() . "</p>\n";
        
        if ($response->getStatusCode() === 200) {
            echo "<p>✅ Home page loads successfully</p>\n";
        } else {
            echo "<p>❌ Home page error status: " . $response->getStatusCode() . "</p>\n";
        }
        
    } else {
        echo "<p>❌ Laravel app not available for route test</p>\n";
    }
} catch (Exception $e) {
    echo "<p>❌ Route Error: " . $e->getMessage() . "</p>\n";
    echo "<p>Error Details: " . $e->getFile() . " Line " . $e->getLine() . "</p>\n";
}

// 5. CHECK LOG FILES
echo "<h2>5. 📋 CHECK LOG FILES</h2>\n";
$logDir = dirname(__DIR__) . '/storage/logs/';
if (is_dir($logDir)) {
    $logs = glob($logDir . '*.log');
    if ($logs) {
        $latestLog = end($logs);
        echo "<p>✅ Latest log: " . basename($latestLog) . "</p>\n";
        
        $logContent = file_get_contents($latestLog);
        $lines = explode("\n", $logContent);
        $recentLines = array_slice($lines, -10); // Last 10 lines
        
        echo "<h4>Recent log entries:</h4>\n";
        echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px; max-height: 200px; overflow-y: auto;'>";
        foreach ($recentLines as $line) {
            if (trim($line)) {
                echo htmlspecialchars($line) . "\n";
            }
        }
        echo "</pre>\n";
    } else {
        echo "<p>⚠️ No log files found</p>\n";
    }
} else {
    echo "<p>❌ Log directory not found: $logDir</p>\n";
}

// 6. QUICK FIXES
echo "<h2>6. 🔧 QUICK FIXES</h2>\n";
echo "<p><a href='?clearconfig=1' style='background: #ffc107; color: black; padding: 8px 16px; text-decoration: none; border-radius: 4px; margin: 5px;'>Clear Config Cache</a></p>\n";
echo "<p><a href='?optimize=1' style='background: #17a2b8; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; margin: 5px;'>Optimize Application</a></p>\n";

// Handle quick fixes
if (isset($_GET['clearconfig'])) {
    try {
        if (isset($app)) {
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            echo "<p>✅ Config and cache cleared</p>\n";
        }
    } catch (Exception $e) {
        echo "<p>❌ Clear cache error: " . $e->getMessage() . "</p>\n";
    }
}

if (isset($_GET['optimize'])) {
    try {
        if (isset($app)) {
            \Illuminate\Support\Facades\Artisan::call('optimize');
            echo "<p>✅ Application optimized</p>\n";
        }
    } catch (Exception $e) {
        echo "<p>❌ Optimize error: " . $e->getMessage() . "</p>\n";
    }
}

echo "<h2>🎯 FINAL TEST</h2>\n";
echo "<p><a href='/' target='_blank' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 18px;'>🏠 OPEN HOME PAGE</a></p>\n";
echo "<p><small>Check browser console for any JavaScript errors</small></p>\n";
?> 