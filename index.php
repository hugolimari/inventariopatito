<?php
declare(strict_types=1);

// Simple router to hide `/public` from URLs.
// Usage with PHP built-in server (recommended for local dev):
// php -S localhost:8000 index.php

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH);

// If someone requests a URL that already contains /public (legacy links),
// strip the prefix so we don't end up mapping to /public/public/...
if (strpos($path, '/public/') === 0) {
    $path = substr($path, 7); // remove leading '/public'
    if ($path === '') {
        $path = '/';
    }
}

$publicPath = __DIR__ . '/public' . $path;

// When using PHP built-in server, let it serve existing static files from /public
if (php_sapi_name() === 'cli-server') {
    if ($path !== '/' && file_exists($publicPath)) {
        return false; // serve the requested resource as-is
    }
}

// Fallback: delegate to public/index.php (front controller)
require_once __DIR__ . '/public/index.php';
