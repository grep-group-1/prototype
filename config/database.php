<?php
/**
 * Database Configuration
 * 
 * Configure your MySQL/MariaDB connection settings here.
 * For XAMPP, the defaults below typically work out of the box.
 * Adjust username/password/port if your installation differs.
 */

return [
    // Database driver (only mysql is supported in this application)
    'driver' => 'mysql',

    // Database host - typically 127.0.0.1 for local XAMPP
    'host' => defined('DB_HOST') ? DB_HOST : '127.0.0.1',

    // Database port - XAMPP default is 3306
    'port' => defined('DB_PORT') ? DB_PORT : 3306,

    // Database name
    'database' => defined('DB_NAME') ? DB_NAME : 'premium_fintech',

    // Database username - XAMPP default is 'root'
    'username' => defined('DB_USER') ? DB_USER : 'root',

    // Database password - XAMPP default is empty
    'password' => defined('DB_PASS') ? DB_PASS : '',

    // Character set
    'charset' => 'utf8mb4',

    // Collation
    'collation' => 'utf8mb4_unicode_ci',

    // PDO options for security and reliability
    'options' => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_STRINGIFY_FETCHES  => false,
    ],
];
