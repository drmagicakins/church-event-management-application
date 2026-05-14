<?php
/**
 * ============================================================================
 * Auth - Authentication service for the admin panel
 * ============================================================================
 * Uses PHP's built-in password_hash() (bcrypt by default) for safe password
 * storage. Never rolls its own crypto. Tracks login attempts to rate-limit
 * brute-force attacks and logs every sign-in/out to activity_logs.
 * ============================================================================
 */

declare(strict_types=1);

namespace App\Core;

class Auth
{
    private Database $db;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->db     = Database::getInstance($config['database']);
    }

    /**
     * Attempt to sign an admin in.
     * Returns the admin row on success, or null on failure.
     */
    public function attempt(string $username, string $password): ?array
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        // Rate-limit by IP
        $recentAttempts = (int)$this->db->fetchValue(
            "SELECT COUNT(*) FROM activity_logs
             WHERE module = 'Authentication'
               AND action LIKE 'Failed login%'
               AND ip_address = ?
               AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)",
            [$ip]
        );
        $limit = $this->config['rate_limit']['login'] ?? 5;
        if ($recentAttempts >= $limit) {
            return null; // locked out; UI shows "Too many attempts"
        }

        $admin = $this->db->fetchOne(
            'SELECT * FROM admins WHERE username = ? AND status = "active" LIMIT 1',
            [$username]
        );

        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            $this->db->insert('activity_logs', [
                'admin_id'   => $admin['id'] ?? null,
                'action'     => "Failed login for username: $username",
                'module'     => 'Authentication',
                'ip_address' => $ip,
                'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250),
            ]);
            return null;
        }

        // Regenerate session to prevent fixation
        session_regenerate_id(true);
        $_SESSION['admin_id']   = (int)$admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_role'] = $admin['role'];
        $_SESSION['csrf_token'] = Csrf::generate();

        // Update last-login metadata
        $this->db->update('admins', [
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $ip,
        ], 'id = ?', [$admin['id']]);

        $this->db->insert('activity_logs', [
            'admin_id'   => $admin['id'],
            'action'     => 'Admin signed in',
            'module'     => 'Authentication',
            'ip_address' => $ip,
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250),
        ]);

        return $admin;
    }

    /** Sign the current admin out. */
    public function logout(): void
    {
        if (!empty($_SESSION['admin_id'])) {
            $this->db->insert('activity_logs', [
                'admin_id'   => $_SESSION['admin_id'],
                'action'     => 'Admin signed out',
                'module'     => 'Authentication',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    /** Is a user currently signed in? */
    public static function check(): bool
    {
        return !empty($_SESSION['admin_id']);
    }

    /** Return the current admin's ID, or null. */
    public static function id(): ?int
    {
        return isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : null;
    }

    /** Hash a plain-text password. */
    public static function hash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /** Generate a signed password-reset token. */
    public function issueResetToken(string $email): ?string
    {
        $admin = $this->db->fetchOne('SELECT id FROM admins WHERE email = ? LIMIT 1', [$email]);
        if (!$admin) return null;

        $token  = bin2hex(random_bytes(32));
        $hashed = hash('sha256', $token);
        $this->db->update('admins', [
            'remember_token' => $hashed . '|' . date('Y-m-d H:i:s', time() + 3600),
        ], 'id = ?', [$admin['id']]);
        return $token;
    }
}
