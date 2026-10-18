<?php
/**
 * Application Bootstrap
 * 
 * Initializes the application: autoloading, configuration,
 * session, database connection, and timezone.
 */

// Set timezone
date_default_timezone_set('Africa/Kampala');

// Error reporting (adjust for production)
$env = 'local';
if (file_exists(__DIR__ . '/../config/app.php')) {
    $appConfig = require __DIR__ . '/../config/app.php';
    $env = $appConfig['env'] ?? 'local';
}

if ($env === 'production') {
    error_reporting(0);
    ini_set('display_errors', '0');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// Define base path constant
if (!defined('APP_BASE_PATH')) {
    define('APP_BASE_PATH', dirname(__DIR__));
}

// Autoloader for App namespace
spl_autoload_register(function (string $class) {
    // Only handle App\ namespace
    $prefix = 'App\\';
    $baseDir = APP_BASE_PATH . '/app/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Load optional .env file if it exists
$envFile = APP_BASE_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            if (!empty($key) && !defined($key)) {
                define($key, $value);
            }
        }
    }
}

// Start session
\App\Services\Auth::getInstance()->startSession();

// Set the application timezone in database
try {
    $db = \App\Services\Database::getInstance();
    $db->query("SET time_zone = '+03:00'");
} catch (\RuntimeException $e) {
    // Database not available yet (e.g., before setup)
    // This is OK for initial page loads
}
