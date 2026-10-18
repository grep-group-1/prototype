<?php
namespace App\Helpers;

/**
 * CSRF Token Management
 * 
 * Generates and validates CSRF tokens to protect against
 * Cross-Site Request Forgery attacks.
 */
class Csrf
{
    /**
     * Generate a new CSRF token and store it in the session.
     */
    public static function generateToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    /**
     * Get the current CSRF token, generating one if needed.
     */
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            return self::generateToken();
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validate a submitted CSRF token.
     */
    public static function validate(?string $token): bool
    {
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Generate an HTML hidden input field containing the CSRF token.
     */
    public static function field(): string
    {
        $token = Escape::html(self::token());
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }
}
