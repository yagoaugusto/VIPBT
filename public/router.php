<?php
// Simple router for PHP built-in server
// This file allows the built-in server to handle pretty URLs

$uri = $_SERVER['REQUEST_URI'];

// Remove query string if present
$uri = parse_url($uri, PHP_URL_PATH);

// If the file exists, serve it directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Otherwise, redirect to index.php with the URL as a parameter
$_GET['url'] = trim($uri, '/');
require_once 'index.php';
?>