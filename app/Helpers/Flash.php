<?php
namespace App\Helpers;

/**
 * Flash Message Helper
 * 
 * Stores messages in the session for one-time display after redirects.
 * Useful for showing success/error messages after form submissions.
 */
class Flash
{
    /**
     * Set a flash message.
     */
    public static function set(string $type, string $message): void
    {
        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = [];
        }
        $_SESSION['flash_messages'][] = [
            'type' => $type,       // success, error, warning, info
            'message' => $message,
        ];
    }

    /**
     * Get all flash messages and clear them from the session.
     */
    public static function getMessages(): array
    {
        $messages = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        return $messages;
    }

    /**
     * Convenience methods for common message types.
     */
    public static function success(string $message): void
    {
        self::set('success', $message);
    }

    public static function error(string $message): void
    {
        self::set('danger', $message);
    }

    public static function warning(string $message): void
    {
        self::set('warning', $message);
    }

    public static function info(string $message): void
    {
        self::set('info', $message);
    }
}
