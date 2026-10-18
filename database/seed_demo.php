<?php
/**
 * Database Seed Script
 * 
 * This script populates the database with demo data including
 * properly hashed passwords. Run this from the command line:
 *   php database/seed_demo.php
 * 
 * Or access it once via browser and then DELETE/RESTRICT it.
 * 
 * SECURITY: Never leave this accessible in production.
 */

require_once __DIR__ . '/../config/database.php';

// Load the Database class
spl_autoload_register(function ($class) {
    $path = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

use App\Services\Database;

echo "=== Premium Fintech Platform - Database Seeder ===\n\n";

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    echo "Connected to database successfully.\n\n";
    
    // Check if users already exist
    $existing = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($existing > 0) {
        echo "WARNING: Database already contains user records.\n";
        echo "This script will UPDATE passwords for existing demo users.\n";
        echo "Type 'yes' to continue: ";
        
        // In CLI mode, read from stdin; in browser, skip check
        if (php_sapi_name() === 'cli') {
            $confirm = trim(fgets(STDIN));
            if ($confirm !== 'yes') {
                echo "Aborted.\n";
                exit(0);
            }
        }
    }
    
    // Generate proper password hashes
    $passwords = [
        'admin@capitaledge.test' => 'Admin@123',
        'funder@capitaledge.test' => 'Funder@123',
        'compliance@capitaledge.test' => 'Compliance@123',
        'finance@capitaledge.test' => 'Finance@123',
    ];
    
    $pdo->beginTransaction();
    
    try {
        foreach ($passwords as $email => $password) {
            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
            
            // Check if user exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Update password
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt->execute([$hash, $email]);
                echo "  Updated password for: {$email}\n";
            } else {
                echo "  WARNING: User {$email} not found. Run schema.sql and seed.sql first.\n";
            }
        }
        
        $pdo->commit();
        echo "\n✓ Demo passwords set successfully.\n\n";
        
    } catch (\Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
    
    // Display credentials
    echo "========================================\n";
    echo "  DEMO ACCOUNT CREDENTIALS\n";
    echo "========================================\n";
    echo "  Admin:      admin@capitaledge.test     / Admin@123\n";
    echo "  Funder:     funder@capitaledge.test    / Funder@123\n";
    echo "  Compliance: compliance@capitaledge.test / Compliance@123\n";
    echo "  Finance:    finance@capitaledge.test   / Finance@123\n";
    echo "========================================\n\n";
    echo "These are SIMULATED accounts for local testing only.\n";
    echo "Do NOT use these credentials in any production environment.\n";
    
} catch (\RuntimeException $e) {
    echo "\nERROR: " . $e->getMessage() . "\n\n";
    echo "Please ensure:\n";
    echo "1. XAMPP MySQL/MariaDB is running\n";
    echo "2. Database 'premium_fintech' has been created\n";
    echo "3. schema.sql has been imported\n";
    echo "4. seed.sql has been imported\n";
    echo "5. Database credentials in config/database.php are correct\n";
    exit(1);
} catch (\Exception $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
    exit(1);
}
