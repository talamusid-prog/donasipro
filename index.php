<?php

/**
 * Laravel Application Entry Point for Shared Hosting
 * This file redirects all requests to the public folder
 */

// Get the current request URI
$requestUri = $_SERVER['REQUEST_URI'];

// Remove query string if present
$requestUri = strtok($requestUri, '?');

// If the request is for the root or doesn't start with /public/, redirect to public folder
if ($requestUri === '/' || $requestUri === '' || !str_starts_with($requestUri, '/public/')) {
    // Build the redirect URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $redirectUrl = $protocol . '://' . $host . '/public' . $requestUri;
    
    // Add query string back if it exists
    if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
        $redirectUrl .= '?' . $_SERVER['QUERY_STRING'];
    }
    
    // Perform redirect
    header('Location: ' . $redirectUrl, true, 301);
    exit;
}

// If we reach here, include the actual Laravel application
require_once __DIR__ . '/public/index.php';
