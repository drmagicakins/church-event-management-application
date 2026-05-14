<?php
/**
 * ============================================================================
 * Csrf - Cross-Site Request Forgery protection
 * ============================================================================
 * Every mutating form embeds a hidden csrf_token field. The server compares
 * that value with the one stored in the session. Tokens are single-use and
 * rotated on every login and every validated submission.
 * ============================================================================
 */

declare(strict_types=1);

namespace App\Core;

class Csrf
{
    /** Generate a new token and store it in the session. */
    public static function generate(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /** Return the <input> tag for embedding in forms. */
    public static function field(): string
    {
        $token = self::generate();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    /** Validate the submitted token; throw if invalid. */
    public static function validate(?string $submitted): void
    {
        if (empty($submitted) || empty($_SESSION['csrf_token'])) {
            http_response_code(419);
            exit('Security token missing. Please reload and try again.');
        }
        if (!hash_equals($_SESSION['csrf_token'], $submitted)) {
            http_response_code(419);
            exit('Security token mismatch. Please reload and try again.');
        }
        // Rotate after successful validation (single-use)
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
