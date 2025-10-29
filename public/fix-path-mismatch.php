<?php
echo "<h1>🔧 FIX PATH MISMATCH - Database vs File Paths</h1>\n";

// 1. CONNECT TO DATABASE
echo "<h2>1. 🗄️ CONNECTING TO DATABASE</h2>\n";

try {
    $envFile = dirname(__DIR__) . '/.env';
    $envContent = file_get_contents($envFile);
    
    // Parse .env variables
    preg_match('/DB_HOST=(.*)/', $envContent, $host_match);
    preg_match('/DB_DATABASE=(.*)/', $envContent, $db_match);
    preg_match('/DB_USERNAME=(.*)/', $envContent, $user_match);
    preg_match('/DB_PASSWORD=(.*)/', $envContent, $pass_match);
    
    $host = trim($host_match[1]);
    $database = trim($db_match[1]);
    $username = trim($user_match[1]);
    $password = trim($pass_match[1]);
    
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✅ Database connected successfully</p>\n";
    echo "<p>Database: $database</p>\n";
    
} catch (Exception $e) {
    echo "<p>❌ Database connection failed: " . $e->getMessage() . "</p>\n";
    exit;
}

// 2. CHECK SLIDERS TABLE
echo "<h2>2. 🎠 CHECKING SLIDERS TABLE</h2>\n";

try {
    $stmt = $pdo->query("SELECT id, title, image_url FROM sliders ORDER BY id");
    $sliders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Found " . count($sliders) . " sliders in database:</p>\n";
    
    foreach ($sliders as $slider) {
        $id = $slider['id'];
        $title = $slider['title'];
        $imageUrl = $slider['image_url'];
        
        echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 10px 0; border-radius: 5px;'>\n";
        echo "<h4>Slider #{$id}: " . htmlspecialchars($title) . "</h4>\n";
        echo "<p><strong>DB Path:</strong> $imageUrl</p>\n";
        
        // Check if file exists at this path
        $localPath = __DIR__ . $imageUrl;
        if (file_exists($localPath)) {
            $size = filesize($localPath);
            echo "<p>✅ File exists: " . number_format($size/1024, 1) . " KB</p>\n";
        } else {
            echo "<p>❌ File NOT found: $localPath</p>\n";
            
            // Try to find the file in different directories
            $filename = basename($imageUrl);
            $searchDirs = ['campaigns', 'categories', 'sliders', 'editor-images'];
            
            foreach ($searchDirs as $dir) {
                $searchPath = __DIR__ . "/storage/$dir/$filename";
                if (file_exists($searchPath)) {
                    echo "<p>📂 Found in: /storage/$dir/$filename</p>\n";
                    $foundPath = "/storage/$dir/$filename";
                    break;
                }
            }
            
            // If found in different location, show fix option
            if (isset($foundPath)) {
                echo "<p><a href='?fix_slider=$id&new_path=" . urlencode($foundPath) . "' style='background: #28a745; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;'>🔧 Fix Path</a></p>\n";
            }
        }
        
        echo "</div>\n";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error querying sliders: " . $e->getMessage() . "</p>\n";
}

// 3. CHECK CAMPAIGNS TABLE
echo "<h2>3. 📋 CHECKING CAMPAIGNS TABLE</h2>\n";

try {
    $stmt = $pdo->query("SELECT id, title, image_url FROM campaigns ORDER BY id DESC LIMIT 10");
    $campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Showing latest 10 campaigns:</p>\n";
    
    foreach ($campaigns as $campaign) {
        $id = $campaign['id'];
        $title = $campaign['title'];
        $imageUrl = $campaign['image_url'];
        
        echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 10px 0; border-radius: 5px;'>\n";
        echo "<h4>Campaign #{$id}: " . htmlspecialchars($title) . "</h4>\n";
        echo "<p><strong>DB Path:</strong> $imageUrl</p>\n";
        
        // Check if file exists
        $localPath = __DIR__ . $imageUrl;
        if (file_exists($localPath)) {
            $size = filesize($localPath);
            echo "<p>✅ File exists: " . number_format($size/1024, 1) . " KB</p>\n";
        } else {
            echo "<p>❌ File NOT found: $localPath</p>\n";
            
            // Try to find the file in different directories
            $filename = basename($imageUrl);
            $searchDirs = ['campaigns', 'categories', 'sliders', 'editor-images'];
            
            foreach ($searchDirs as $dir) {
                $searchPath = __DIR__ . "/storage/$dir/$filename";
                if (file_exists($searchPath)) {
                    echo "<p>📂 Found in: /storage/$dir/$filename</p>\n";
                    $foundPath = "/storage/$dir/$filename";
                    break;
                }
            }
            
            // If found in different location, show fix option
            if (isset($foundPath)) {
                echo "<p><a href='?fix_campaign=$id&new_path=" . urlencode($foundPath) . "' style='background: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;'>🔧 Fix Path</a></p>\n";
            }
        }
        
        echo "</div>\n";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error querying campaigns: " . $e->getMessage() . "</p>\n";
}

// 4. HANDLE PATH FIXES
if (isset($_GET['fix_slider'])) {
    $sliderId = (int)$_GET['fix_slider'];
    $newPath = $_GET['new_path'];
    
    try {
        $stmt = $pdo->prepare("UPDATE sliders SET image_url = ? WHERE id = ?");
        $stmt->execute([$newPath, $sliderId]);
        
        echo "<p>✅ Updated slider #$sliderId path to: $newPath</p>\n";
    } catch (Exception $e) {
        echo "<p>❌ Failed to update slider path: " . $e->getMessage() . "</p>\n";
    }
}

if (isset($_GET['fix_campaign'])) {
    $campaignId = (int)$_GET['fix_campaign'];
    $newPath = $_GET['new_path'];
    
    try {
        $stmt = $pdo->prepare("UPDATE campaigns SET image_url = ? WHERE id = ?");
        $stmt->execute([$newPath, $campaignId]);
        
        echo "<p>✅ Updated campaign #$campaignId path to: $newPath</p>\n";
    } catch (Exception $e) {
        echo "<p>❌ Failed to update campaign path: " . $e->getMessage() . "</p>\n";
    }
}

// 5. BULK PATH ANALYSIS
echo "<h2>5. 📊 BULK PATH ANALYSIS</h2>\n";

try {
    // Get all unique image paths from both tables
    $allPaths = [];
    
    $stmt = $pdo->query("SELECT DISTINCT image_url FROM sliders WHERE image_url IS NOT NULL");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $allPaths[] = $row['image_url'];
    }
    
    $stmt = $pdo->query("SELECT DISTINCT image_url FROM campaigns WHERE image_url IS NOT NULL");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $allPaths[] = $row['image_url'];
    }
    
    $allPaths = array_unique($allPaths);
    
    $foundFiles = 0;
    $missingFiles = 0;
    $pathMismatches = [];
    
    foreach ($allPaths as $path) {
        $localPath = __DIR__ . $path;
        if (file_exists($localPath)) {
            $foundFiles++;
        } else {
            $missingFiles++;
            
            // Check if file exists in different directory
            $filename = basename($path);
            $searchDirs = ['campaigns', 'categories', 'sliders', 'editor-images', 'payment_proofs', 'bank-logos'];
            
            foreach ($searchDirs as $dir) {
                $searchPath = __DIR__ . "/storage/$dir/$filename";
                if (file_exists($searchPath)) {
                    $pathMismatches[] = [
                        'db_path' => $path,
                        'actual_path' => "/storage/$dir/$filename",
                        'filename' => $filename
                    ];
                    break;
                }
            }
        }
    }
    
    echo "<p><strong>Summary:</strong></p>\n";
    echo "<p>✅ Files found: $foundFiles</p>\n";
    echo "<p>❌ Files missing: $missingFiles</p>\n";
    echo "<p>🔧 Path mismatches: " . count($pathMismatches) . "</p>\n";
    
    if (!empty($pathMismatches)) {
        echo "<h4>Path Mismatches Found:</h4>\n";
        foreach ($pathMismatches as $mismatch) {
            echo "<p>DB: {$mismatch['db_path']} → Actual: {$mismatch['actual_path']}</p>\n";
        }
        
        echo "<p><a href='?fix_all_paths=1' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔧 FIX ALL PATH MISMATCHES</a></p>\n";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error in bulk analysis: " . $e->getMessage() . "</p>\n";
}

// 6. BULK FIX ALL PATHS
if (isset($_GET['fix_all_paths'])) {
    echo "<h2>6. 🔧 BULK FIXING ALL PATHS</h2>\n";
    
    try {
        $fixed = 0;
        
        // Fix sliders
        $stmt = $pdo->query("SELECT id, image_url FROM sliders WHERE image_url IS NOT NULL");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $oldPath = $row['image_url'];
            $localPath = __DIR__ . $oldPath;
            
            if (!file_exists($localPath)) {
                $filename = basename($oldPath);
                $searchDirs = ['campaigns', 'categories', 'sliders', 'editor-images', 'payment_proofs', 'bank-logos'];
                
                foreach ($searchDirs as $dir) {
                    $searchPath = __DIR__ . "/storage/$dir/$filename";
                    if (file_exists($searchPath)) {
                        $newPath = "/storage/$dir/$filename";
                        $updateStmt = $pdo->prepare("UPDATE sliders SET image_url = ? WHERE id = ?");
                        $updateStmt->execute([$newPath, $id]);
                        echo "<p>✅ Fixed slider #$id: $oldPath → $newPath</p>\n";
                        $fixed++;
                        break;
                    }
                }
            }
        }
        
        // Fix campaigns  
        $stmt = $pdo->query("SELECT id, image_url FROM campaigns WHERE image_url IS NOT NULL");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $oldPath = $row['image_url'];
            $localPath = __DIR__ . $oldPath;
            
            if (!file_exists($localPath)) {
                $filename = basename($oldPath);
                $searchDirs = ['campaigns', 'categories', 'sliders', 'editor-images', 'payment_proofs', 'bank-logos'];
                
                foreach ($searchDirs as $dir) {
                    $searchPath = __DIR__ . "/storage/$dir/$filename";
                    if (file_exists($searchPath)) {
                        $newPath = "/storage/$dir/$filename";
                        $updateStmt = $pdo->prepare("UPDATE campaigns SET image_url = ? WHERE id = ?");
                        $updateStmt->execute([$newPath, $id]);
                        echo "<p>✅ Fixed campaign #$id: $oldPath → $newPath</p>\n";
                        $fixed++;
                        break;
                    }
                }
            }
        }
        
        echo "<p><strong>🎉 Fixed $fixed path mismatches!</strong></p>\n";
        
    } catch (Exception $e) {
        echo "<p>❌ Error fixing paths: " . $e->getMessage() . "</p>\n";
    }
}

echo "<h2>🎯 FINAL TEST</h2>\n";
echo "<p><a href='/' target='_blank' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 18px;'>🏠 TEST WEBSITE NOW</a></p>\n";
echo "<p>After fixing path mismatches, your website should display all images correctly.</p>\n";
?> 