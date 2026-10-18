<?php
namespace App\Services;

use App\Helpers\Csrf;
use App\Helpers\Flash;

/**
 * Session and Authentication Service
 * 
 * Manages user sessions, login, logout, and authentication state.
 * Uses PHP's built-in session handling with secure configuration.
 */
class Auth
{
    private static ?Auth $instance = null;
    private ?array $user = null;
    private bool $started = false;

    /**
     * Start the session if not already started.
     */
    public function startSession(): void
    {
        if ($this->started || session_status() === PHP_SESSION_ACTIVE) {
            $this->started = true;
            return;
        }

        $config = require dirname(__DIR__, 2) . '/config/app.php';
        $sessionConfig = $config['session'];

        session_name($sessionConfig['name']);
        
        // Secure session cookie settings
        session_set_cookie_params([
            'lifetime' => $sessionConfig['lifetime'],
            'path' => $sessionConfig['path'],
            'domain' => '',
            'secure' => $sessionConfig['secure'],
            'httponly' => $sessionConfig['httponly'],
            'samesite' => $sessionConfig['samesite'],
        ]);

        session_start();
        $this->started = true;

        // Check if session has expired
        if (isset($_SESSION['last_activity'])) {
            $elapsed = time() - $_SESSION['last_activity'];
            if ($elapsed > $sessionConfig['lifetime']) {
                $this->logout();
                session_start(); // Restart after destroying
            }
        }
        $_SESSION['last_activity'] = time();
    }

    /**
     * Attempt to log in a user with email and password.
     * 
     * @return array{success: bool, message: string}
     */
    public function attempt(string $email, string $password): array
    {
        $db = Database::getInstance();

        // Check for account lockout
        if ($this->isLockedOut($email)) {
            return ['success' => false, 'message' => 'Account is temporarily locked. Please try again later.'];
        }

        // Find user by email
        $user = $db->fetchOne(
            "SELECT u.*, r.name as role_name
             FROM users u
             LEFT JOIN user_roles ur ON u.id = ur.user_id
             LEFT JOIN roles r ON ur.role_id = r.id
             WHERE u.email = ?",
            [$email]
        );

        if (!$user) {
            $this->recordFailedAttempt($email);
            return ['success' => false, 'message' => 'Invalid credentials.'];
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            $this->recordFailedAttempt($email);
            return ['success' => false, 'message' => 'Invalid credentials.'];
        }

        // Check if account is active
        if ($user['status'] !== 'active') {
            return ['success' => false, 'message' => 'Your account is not active. Please contact support.'];
        }

        // Clear failed attempts on successful login
        $this->clearFailedAttempts($email);

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        // Store user in session (exclude password)
        unset($user['password']);
        $_SESSION['user'] = $user;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role_name'];

        $this->user = $user;

        // Update last login timestamp
        $db->query(
            "UPDATE users SET last_login_at = NOW() WHERE id = ?",
            [$user['id']]
        );

        return ['success' => true, 'message' => 'Login successful.'];
    }

    /**
     * Log out the current user.
     */
    public function logout(): void
    {
        $this->user = null;
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $this->started = false;
    }

    /**
     * Check if a user is currently logged in.
     */
    public function check(): bool
    {
        return isset($_SESSION['user']) && $_SESSION['user'] !== null;
    }

    /**
     * Get the currently authenticated user.
     */
    public function user(): ?array
    {
        if ($this->user === null && isset($_SESSION['user'])) {
            $this->user = $_SESSION['user'];
        }
        return $this->user;
    }

    /**
     * Get the current user's ID.
     */
    public function userId(): ?int
    {
        $user = $this->user();
        return $user ? (int)$user['id'] : null;
    }

    /**
     * Get the current user's role name.
     */
    public function userRole(): ?string
    {
        $user = $this->user();
        return $user['role_name'] ?? null;
    }

    /**
     * Check if the current user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->userRole() === $role;
    }

    /**
     * Check if the current user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if the current user is a funder.
     */
    public function isFunder(): bool
    {
        return $this->hasRole('funder');
    }

    /**
     * Check if the current user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->userRole(), $roles, true);
    }

    /**
     * Check if account is locked out due to failed login attempts.
     */
    private function isLockedOut(string $email): bool
    {
        $config = require dirname(__DIR__, 2) . '/config/app.php';
        $lockoutKey = 'login_attempts_' . md5($email);

        if (!isset($_SESSION[$lockoutKey])) {
            return false;
        }

        $attempts = $_SESSION[$lockoutKey];
        if ($attempts['count'] >= $config['max_login_attempts']) {
            $elapsed = time() - $attempts['first_attempt'];
            if ($elapsed < $config['lockout_duration']) {
                return true;
            }
            // Lockout period has passed, reset
            unset($_SESSION[$lockoutKey]);
        }

        return false;
    }

    /**
     * Record a failed login attempt.
     */
    private function recordFailedAttempt(string $email): void
    {
        $lockoutKey = 'login_attempts_' . md5($email);
        if (!isset($_SESSION[$lockoutKey])) {
            $_SESSION[$lockoutKey] = [
                'count' => 0,
                'first_attempt' => time(),
            ];
        }
        $_SESSION[$lockoutKey]['count']++;
    }

    /**
     * Clear failed login attempts after successful login.
     */
    private function clearFailedAttempts(string $email): void
    {
        $lockoutKey = 'login_attempts_' . md5($email);
        unset($_SESSION[$lockoutKey]);
    }

    /**
     * Get the singleton instance.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
