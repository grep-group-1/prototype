<?php
namespace App\Helpers;

/**
 * Output escaping helper functions.
 * All user-supplied data must be escaped before rendering in HTML.
 */
class Escape
{
    /**
     * Escape a string for safe HTML output.
     */
    public static function html(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Escape a string for use in a URL query parameter.
     */
    public static function url(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        return urlencode($value);
    }

    /**
     * Escape a string for use in a JavaScript string literal.
     */
    public static function js(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        return json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }

    /**
     * Escape a string for use in an HTML attribute.
     */
    public static function attr(?string $value): string
    {
        return self::html($value);
    }
}
