<?php
/**
 * Application Configuration
 * 
 * Central configuration for the Premium Fintech Platform.
 * Adjust these values for your local XAMPP environment.
 */

return [
    // Application name displayed in the UI
    'name' => 'CapitalEdge Funding',

    // Application environment: 'local', 'development', 'production'
    'env' => 'local',

    // Whether the application is in test/demo mode
    'test_mode' => true,

    // Base URL - adjust if installed in a subdirectory
    // Examples:
    //   http://localhost/premium-fintech/
    //   http://localhost:8080/
    'base_url' => defined('APP_BASE_URL')
        ? APP_BASE_URL
        : (function() {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
            // If running from public/ subdirectory, adjust accordingly
            if (basename($scriptDir) === 'public') {
                $scriptDir = dirname($scriptDir);
            }
            return $protocol . '://' . $host . ($scriptDir !== '/' && $scriptDir !== '' ? $scriptDir : '') . '/';
        })(),

    // Application root directory (parent of public/)
    'base_path' => defined('APP_BASE_PATH')
        ? APP_BASE_PATH
        : dirname(__DIR__),

    // Default currency code
    'currency' => 'UGX',

    // Currency symbol
    'currency_symbol' => 'UGX ',

    // Number of decimal places for display
    'currency_decimals' => 0,

    // Session configuration
    'session' => [
        'name' => 'pf_session',
        'lifetime' => 7200,     // 2 hours
        'path' => '/',
        'secure' => false,      // Set true for HTTPS
        'httponly' => true,
        'samesite' => 'Lax',
    ],

    // Pagination defaults
    'per_page' => 20,

    // Maximum login attempts before lockout
    'max_login_attempts' => 5,

    // Lockout duration in seconds
    'lockout_duration' => 900,  // 15 minutes

    // Supported roles
    'roles' => [
        'funder' => 'Funder',
        'admin' => 'Administrator',
        'compliance' => 'Compliance Reviewer',
        'finance' => 'Finance Operator',
    ],

    // Timezone
    'timezone' => 'Africa/Kampala',
];
