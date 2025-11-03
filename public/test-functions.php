<?php
// Test file untuk memastikan fallback functions bekerja

echo "<h1>Test Fallback Functions</h1>";

// Load helpers
require_once '../app/helpers.php';

echo "<h2>BCMath Functions Test:</h2>";
echo "<ul>";

// Test BCMath functions
if (function_exists('bcadd')) {
    echo "<li>✅ bcadd: " . bcadd('1.234', '5.678', 3) . "</li>";
} else {
    echo "<li>❌ bcadd: Function not available</li>";
}

if (function_exists('bcmul')) {
    echo "<li>✅ bcmul: " . bcmul('2.5', '3.5', 2) . "</li>";
} else {
    echo "<li>❌ bcmul: Function not available</li>";
}

if (function_exists('bcdiv')) {
    echo "<li>✅ bcdiv: " . bcdiv('10', '3', 3) . "</li>";
} else {
    echo "<li>❌ bcdiv: Function not available</li>";
}

echo "</ul>";

echo "<h2>Mbstring Functions Test:</h2>";
echo "<ul>";

// Test Mbstring functions
if (function_exists('mb_strlen')) {
    echo "<li>✅ mb_strlen: " . mb_strlen('Hello World') . "</li>";
} else {
    echo "<li>❌ mb_strlen: Function not available</li>";
}

if (function_exists('mb_substr')) {
    echo "<li>✅ mb_substr: " . mb_substr('Hello World', 0, 5) . "</li>";
} else {
    echo "<li>❌ mb_substr: Function not available</li>";
}

if (function_exists('mb_strtolower')) {
    echo "<li>✅ mb_strtolower: " . mb_strtolower('HELLO WORLD') . "</li>";
} else {
    echo "<li>❌ mb_strtolower: Function not available</li>";
}

if (function_exists('mb_strtoupper')) {
    echo "<li>✅ mb_strtoupper: " . mb_strtoupper('hello world') . "</li>";
} else {
    echo "<li>❌ mb_strtoupper: Function not available</li>";
}

echo "</ul>";

echo "<h2>Laravel Basic Test:</h2>";

// Test if we can load Laravel
try {
    require_once '../vendor/autoload.php';
    echo "<p>✅ Autoloader loaded successfully</p>";
    
    // Test if we can create Laravel app
    $app = require_once '../bootstrap/app.php';
    echo "<p>✅ Laravel app created successfully</p>";
    
    // Test basic Laravel functions
    if (function_exists('app')) {
        echo "<p>✅ Laravel app() function available</p>";
    } else {
        echo "<p>❌ Laravel app() function not available</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error loading Laravel: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p>❌ Fatal error loading Laravel: " . $e->getMessage() . "</p>";
}

echo "<h2>File Structure Test:</h2>";
echo "<ul>";
echo "<li>app/helpers.php exists: " . (file_exists('../app/helpers.php') ? '✅' : '❌') . "</li>";
echo "<li>vendor/autoload.php exists: " . (file_exists('../vendor/autoload.php') ? '✅' : '❌') . "</li>";
echo "<li>bootstrap/app.php exists: " . (file_exists('../bootstrap/app.php') ? '✅' : '❌') . "</li>";
echo "<li>.env exists: " . (file_exists('../.env') ? '✅' : '❌') . "</li>";
echo "</ul>";
?> 