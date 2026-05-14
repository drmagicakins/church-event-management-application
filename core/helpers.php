<?php
/**
 * ============================================================================
 * Global helper functions
 * ============================================================================
 * Loaded via composer.json autoload.files so they're available everywhere.
 * ============================================================================
 */

declare(strict_types=1);

/** Escape a string for safe HTML output. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Build a URL with the configured APP_URL base. */
function url(string $path = ''): string
{
    $base = rtrim($_ENV['APP_URL'] ?? '', '/');
    return $base . '/' . ltrim($path, '/');
}

/** Redirect with a flash message. */
function redirect_with(string $url, string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    header("Location: $url");
    exit;
}

/** Pop the flash message (call once in a view). */
function flash(): ?array
{
    if (empty($_SESSION['flash'])) return null;
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}

/** Return the authenticated admin's ID or null. */
function auth_id(): ?int
{
    return isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : null;
}

/** Simple slug generator. */
function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text) ?: $text;
    $text = strtolower(trim($text, '-'));
    $text = preg_replace('~-+~', '-', $text);
    return $text ?: uniqid();
}

/** Format a MySQL datetime for display. */
function format_date(?string $value, string $format = 'M j, Y'): string
{
    if (!$value) return '';
    return date($format, strtotime($value));
}

/** Format money with the given currency symbol. */
function format_money($amount, string $currency = 'NGN'): string
{
    return $currency . ' ' . number_format((float)$amount, 0);
}
