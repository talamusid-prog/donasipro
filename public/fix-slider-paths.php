<?php
echo "<h1>🔧 FIX SLIDER PATHS - Database Correction</h1>\n";

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

// 2. CHECK TABLE STRUCTURE
echo "<h2>2. 📋 CHECKING SLIDERS TABLE STRUCTURE</h2>\n";

try {
    $stmt = $pdo->query("DESCRIBE sliders");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Sliders table columns:</p>\n";
    $imageColumn = null;
    
    foreach ($columns as $column) {
        $columnName = $column['Field'];
        $columnType = $column['Type'];
        echo "<p>• <strong>$columnName</strong> ($columnType)</p>\n";
        
        // Look for image-related columns
        if (strpos(strtolower($columnName), 'image') !== false || 
            strpos(strtolower($columnName), 'url') !== false ||
            strpos(strtolower($columnName), 'path') !== false) {
            $imageColumn = $columnName;
        }
    }
    
    if ($imageColumn) {
        echo "<p>✅ Found image column: <strong>$imageColumn</strong></p>\n";
    } else {
        echo "<p>❌ No image column found. Available columns:</p>\n";
        foreach ($columns as $column) {
            echo "<p>• {$column['Field']}</p>\n";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error checking table structure: " . $e->getMessage() . "</p>\n";
    exit;
}

// 3. SHOW CURRENT SLIDERS WITH CORRECT COLUMN
echo "<h2>3. 🎠 CURRENT SLIDERS IN DATABASE</h2>\n";

if ($imageColumn) {
    try {
        $stmt = $pdo->query("SELECT id, title, $imageColumn FROM sliders ORDER BY id");
        $sliders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p>Found " . count($sliders) . " sliders:</p>\n";
        
        foreach ($sliders as $slider) {
            $id = $slider['id'];
            $title = $slider['title'];
            $imagePath = $slider[$imageColumn];
            
            echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 10px 0; border-radius: 5px;'>\n";
            echo "<h4>Slider #{$id}: " . htmlspecialchars($title) . "</h4>\n";
            echo "<p><strong>Current Path:</strong> $imagePath</p>\n";
            
            // Check if file exists
            $localPath = __DIR__ . $imagePath;
            if (file_exists($localPath)) {
                $size = filesize($localPath);
                echo "<p>✅ File exists: " . number_format($size/1024, 1) . " KB</p>\n";
            } else {
                echo "<p>❌ File NOT found: $localPath</p>\n";
            }
            
            echo "</div>\n";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Error querying sliders: " . $e->getMessage() . "</p>\n";
    }
}

// 4. SPECIFIC FIX FOR THE PROBLEMATIC SLIDER
echo "<h2>4. 🔧 SPECIFIC SLIDER PATH FIX</h2>\n";

$problematicPath = '/storage/sliders/1750875710_1742635102550-333511374.jpg';
$correctPath = '/storage/campaigns/1751022642_1738827561whatsapp_image_2025_02_05_at_9_46_29_am_9_jpeg.webp';

echo "<p><strong>Problematic Path:</strong> $problematicPath</p>\n";
echo "<p><strong>Correct Path:</strong> $correctPath</p>\n";

// Check if both files exist
$problematicLocal = __DIR__ . $problematicPath;
$correctLocal = __DIR__ . $correctPath;

echo "<h3>File Existence Check:</h3>\n";
if (file_exists($problematicLocal)) {
    $size = filesize($problematicLocal);
    echo "<p>✅ Problematic file exists: " . number_format($size/1024, 1) . " KB</p>\n";
} else {
    echo "<p>❌ Problematic file NOT found</p>\n";
}

if (file_exists($correctLocal)) {
    $size = filesize($correctLocal);
    echo "<p>✅ Correct file exists: " . number_format($size/1024, 1) . " KB</p>\n";
} else {
    echo "<p>❌ Correct file NOT found</p>\n";
}

// 5. FIND SLIDER WITH PROBLEMATIC PATH
echo "<h2>5. 🔍 FINDING SLIDER WITH PROBLEMATIC PATH</h2>\n";

if ($imageColumn) {
    try {
        $stmt = $pdo->prepare("SELECT id, title, $imageColumn FROM sliders WHERE $imageColumn = ?");
        $stmt->execute([$problematicPath]);
        $problematicSlider = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($problematicSlider) {
            echo "<p>✅ Found slider with problematic path:</p>\n";
            echo "<p><strong>ID:</strong> {$problematicSlider['id']}</p>\n";
            echo "<p><strong>Title:</strong> " . htmlspecialchars($problematicSlider['title']) . "</p>\n";
            echo "<p><strong>Current Path:</strong> {$problematicSlider[$imageColumn]}</p>\n";
            
            // Show fix button
            echo "<p><a href='?fix_slider={$problematicSlider['id']}&new_path=" . urlencode($correctPath) . "&column=" . urlencode($imageColumn) . "' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔧 Fix This Slider Path</a></p>\n";
            
        } else {
            echo "<p>❌ No slider found with problematic path: $problematicPath</p>\n";
            
            // Search for similar paths
            $stmt = $pdo->prepare("SELECT id, title, $imageColumn FROM sliders WHERE $imageColumn LIKE ?");
            $stmt->execute(['%1750875710_1742635102550-333511374.jpg%']);
            $similarSliders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($similarSliders) {
                echo "<p>Found sliders with similar filename:</p>\n";
                foreach ($similarSliders as $slider) {
                    echo "<p>• ID {$slider['id']}: {$slider[$imageColumn]}</p>\n";
                    echo "<p><a href='?fix_slider={$slider['id']}&new_path=" . urlencode($correctPath) . "&column=" . urlencode($imageColumn) . "' style='background: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;'>Fix</a></p>\n";
                }
            }
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Error finding slider: " . $e->getMessage() . "</p>\n";
    }
}

// 6. HANDLE PATH FIX
if (isset($_GET['fix_slider']) && isset($_GET['new_path']) && isset($_GET['column'])) {
    $sliderId = (int)$_GET['fix_slider'];
    $newPath = $_GET['new_path'];
    $column = $_GET['column'];
    
    echo "<h2>6. 🔧 APPLYING PATH FIX</h2>\n";
    
    try {
        // Get current slider info
        $stmt = $pdo->prepare("SELECT title, $column FROM sliders WHERE id = ?");
        $stmt->execute([$sliderId]);
        $currentSlider = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($currentSlider) {
            $oldPath = $currentSlider[$column];
            $title = $currentSlider['title'];
            
            echo "<p><strong>Slider:</strong> $title</p>\n";
            echo "<p><strong>Old Path:</strong> $oldPath</p>\n";
            echo "<p><strong>New Path:</strong> $newPath</p>\n";
            
            // Update the path
            $updateStmt = $pdo->prepare("UPDATE sliders SET $column = ? WHERE id = ?");
            $updateStmt->execute([$newPath, $sliderId]);
            
            echo "<p>✅ Successfully updated slider #$sliderId path!</p>\n";
            echo "<p>Changed: $oldPath → $newPath</p>\n>";
            
        } else {
            echo "<p>❌ Slider not found</p>\n";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Error updating slider: " . $e->getMessage() . "</p>\n";
    }
}

// 7. BULK FIX ALL SLIDER PATHS
echo "<h2>7. 🚀 BULK FIX ALL SLIDER PATHS</h2>\n";

if ($imageColumn) {
    echo "<p><a href='?bulk_fix=1&column=" . urlencode($imageColumn) . "' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔧 BULK FIX ALL SLIDER PATHS</a></p>\n";
}

if (isset($_GET['bulk_fix']) && isset($_GET['column'])) {
    $column = $_GET['column'];
    echo "<h3>Bulk Fix Results:</h3>\n";
    
    try {
        $fixed = 0;
        
        // Get all sliders
        $stmt = $pdo->query("SELECT id, title, $column FROM sliders");
        $allSliders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($allSliders as $slider) {
            $id = $slider['id'];
            $title = $slider['title'];
            $imagePath = $slider[$column];
            
            // Check if file exists at current path
            $localPath = __DIR__ . $imagePath;
            if (!file_exists($localPath)) {
                // Try to find the file in different directories
                $filename = basename($imagePath);
                $searchDirs = ['campaigns', 'categories', 'sliders', 'editor-images'];
                
                foreach ($searchDirs as $dir) {
                    $searchPath = __DIR__ . "/storage/$dir/$filename";
                    if (file_exists($searchPath)) {
                        $newPath = "/storage/$dir/$filename";
                        
                        // Update database
                        $updateStmt = $pdo->prepare("UPDATE sliders SET $column = ? WHERE id = ?");
                        $updateStmt->execute([$newPath, $id]);
                        
                        echo "<p>✅ Fixed slider #$id ($title): $imagePath → $newPath</p>\n";
                        $fixed++;
                        break;
                    }
                }
            }
        }
        
        echo "<p><strong>🎉 Bulk fix completed! Fixed $fixed slider paths.</strong></p>\n";
        
    } catch (Exception $e) {
        echo "<p>❌ Error in bulk fix: " . $e->getMessage() . "</p>\n";
    }
}

// 8. FINAL TEST
echo "<h2>8. 🎯 FINAL TEST</h2>\n";
$protocol = 'https';
$domain = $_SERVER['HTTP_HOST'];

echo "<p><a href='$protocol://$domain/' target='_blank' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 18px;'>🏠 TEST WEBSITE NOW</a></p>\n";
echo "<p><a href='$protocol://$domain/storage/campaigns/1751022642_1738827561whatsapp_image_2025_02_05_at_9_46_29_am_9_jpeg.webp' target='_blank' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🖼️ Test Correct Image</a></p>\n";

echo "<h2>🎉 SLIDER PATH FIX COMPLETE!</h2>\n";
echo "<p>After fixing the paths, your slider images should display correctly.</p>\n";
?>