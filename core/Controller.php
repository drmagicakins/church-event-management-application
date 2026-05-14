<?php
/**
 * ============================================================================
 * Controller - Base class for all MVC controllers
 * ============================================================================
 * Provides shared helpers for:
 *   - Rendering views with variables
 *   - JSON responses
 *   - Redirects
 *   - Input sanitization
 *   - File upload handling
 * ============================================================================
 */

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected array $config;
    protected Database $db;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->db     = Database::getInstance($config['database']);
    }

    /** Render a view file with the given data. */
    protected function view(string $viewName, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $file = $this->config['paths']['views'] . '/' . str_replace('.', '/', $viewName) . '.php';
        if (!file_exists($file)) {
            throw new \RuntimeException("View not found: $viewName");
        }
        require $file;
    }

    /** Send a JSON response and terminate. */
    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /** Redirect to another URL. */
    protected function redirect(string $url, int $status = 302): void
    {
        http_response_code($status);
        header("Location: $url");
        exit;
    }

    /** Safely read a POST value. */
    protected function post(string $key, $default = null)
    {
        if (!isset($_POST[$key])) return $default;
        return is_string($_POST[$key]) ? trim($_POST[$key]) : $_POST[$key];
    }

    /** Safely read a GET value. */
    protected function get(string $key, $default = null)
    {
        if (!isset($_GET[$key])) return $default;
        return is_string($_GET[$key]) ? trim($_GET[$key]) : $_GET[$key];
    }

    /** HTML-escape any string for safe output. */
    protected function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Handle a single file upload securely.
     * Returns the relative path to the saved file, or null on failure.
     */
    protected function upload(array $file, string $subfolder): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return null;

        $maxBytes = ($this->config['upload']['max_mb'] ?? 8) * 1024 * 1024;
        if ($file['size'] > $maxBytes) return null;

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = $this->config['upload']['allowed_ext'] ?? [];
        if (!in_array($ext, $allowed, true)) return null;

        // Reject based on MIME, not just extension
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        $allowedMimes = [
            'jpg'  => ['image/jpeg'], 'jpeg' => ['image/jpeg'],
            'png'  => ['image/png'],  'gif'  => ['image/gif'],
            'webp' => ['image/webp'], 'pdf'  => ['application/pdf'],
            'mp3'  => ['audio/mpeg'], 'mp4'  => ['video/mp4'],
        ];
        if (!in_array($mime, $allowedMimes[$ext] ?? [], true)) return null;

        $destDir  = rtrim($this->config['upload']['destination'], '/') . '/' . trim($subfolder, '/');
        if (!is_dir($destDir)) mkdir($destDir, 0755, true);

        $newName = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest    = $destDir . '/' . $newName;
        if (!move_uploaded_file($file['tmp_name'], $dest)) return null;

        return '/uploads/' . trim($subfolder, '/') . '/' . $newName;
    }

    /** Log an admin activity to the activity_logs table. */
    protected function logActivity(string $action, string $module, array $metadata = []): void
    {
        if (empty($_SESSION['admin_id'])) return;
        $this->db->insert('activity_logs', [
            'admin_id'   => $_SESSION['admin_id'],
            'action'     => $action,
            'module'     => $module,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250),
            'metadata'   => json_encode($metadata),
        ]);
    }
}
