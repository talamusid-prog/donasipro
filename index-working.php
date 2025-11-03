<?php
/**
 * Working Laravel Entry Point for Shared Hosting
 * Simple redirect to public folder
 */

// Get the current request URI
$requestUri = $_SERVER['REQUEST_URI'];

// Remove query string if present
$requestUri = strtok($requestUri, '?');

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
