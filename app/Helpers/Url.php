<?php
namespace App\Helpers;

/**
 * URL and path helper functions.
 */
class Url
{
    private static ?string $baseUrl = null;

    /**
     * Set the base URL explicitly.
     */
    public static function setBaseUrl(string $url): void
    {
        self::$baseUrl = rtrim($url, '/') . '/';
    }

    /**
     * Get the base URL of the application.
     */
    public static function base(string $path = ''): string
    {
        if (self::$baseUrl === null) {
            $config = require dirname(__DIR__, 2) . '/config/app.php';
            self::$baseUrl = $config['base_url'];
        }
        return self::$baseUrl . ltrim($path, '/');
    }

    /**
     * Generate a URL for a given route.
     */
    public static function to(string $route, array $params = []): string
    {
        $url = self::base($route);
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        return $url;
    }

    /**
     * Generate an asset URL.
     */
    public static function asset(string $path): string
    {
        return self::base('assets/' . ltrim($path, '/'));
    }

    /**
     * Get the current URL.
     */
    public static function current(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        return $protocol . '://' . $host . $uri;
    }
}
