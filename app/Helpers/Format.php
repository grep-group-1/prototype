<?php
namespace App\Helpers;

/**
 * Formatting Helper
 * 
 * Provides formatting functions for currency, dates, and numbers.
 */
class Format
{
    /**
     * Format a monetary amount.
     * Uses string-based formatting to avoid floating-point issues.
     * 
     * @param string $amount The amount as a string (e.g., "1500000")
     * @param string $currencyCode Currency code (e.g., "UGX")
     * @return string Formatted amount
     */
    public static function currency(?string $amount, string $currencyCode = 'UGX'): string
    {
        if ($amount === null || $amount === '') {
            return $currencyCode . ' 0';
        }

        // Format with thousands separators
        // We work with strings to avoid floating-point issues
        $negative = false;
        if (str_starts_with($amount, '-')) {
            $negative = true;
            $amount = substr($amount, 1);
        }

        // Split on decimal point
        $parts = explode('.', $amount);
        $integer = $parts[0];
        
        // Add thousands separators
        $formatted = '';
        $len = strlen($integer);
        for ($i = 0; $i < $len; $i++) {
            if ($i > 0 && ($len - $i) % 3 === 0) {
                $formatted .= ',';
            }
            $formatted .= $integer[$i];
        }

        return ($negative ? '-' : '') . $currencyCode . ' ' . $formatted;
    }

    /**
     * Format a date for display.
     */
    public static function date(?string $datetime): string
    {
        if (empty($datetime)) {
            return '—';
        }
        try {
            $date = new \DateTime($datetime);
            return $date->format('d M Y');
        } catch (\Exception $e) {
            return '—';
        }
    }

    /**
     * Format a date and time for display.
     */
    public static function dateTime(?string $datetime): string
    {
        if (empty($datetime)) {
            return '—';
        }
        try {
            $date = new \DateTime($datetime);
            return $date->format('d M Y, H:i');
        } catch (\Exception $e) {
            return '—';
        }
    }

    /**
     * Format a relative time (e.g., "2 hours ago").
     */
    public static function timeAgo(?string $datetime): string
    {
        if (empty($datetime)) {
            return '—';
        }
        try {
            $date = new \DateTime($datetime);
            $now = new \DateTime();
            $diff = $now->diff($date);

            if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
            if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
            if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
            if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
            if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
            return 'Just now';
        } catch (\Exception $e) {
            return '—';
        }
    }

    /**
     * Format a percentage value.
     */
    public static function percentage(?string $value): string
    {
        if ($value === null || $value === '') {
            return '0%';
        }
        return rtrim(rtrim($value, '0'), '.') . '%';
    }

    /**
     * Truncate a string to a maximum length.
     */
    public static function truncate(?string $text, int $length = 100): string
    {
        if ($text === null) {
            return '';
        }
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length) . '…';
    }
}
