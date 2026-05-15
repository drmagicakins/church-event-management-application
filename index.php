<?php
/**
 * ============================================================================
 * Team Praise Official - Front Controller
 * ============================================================================
 * All HTTP requests are routed through this file via .htaccess (Apache) or
 * Nginx try_files. It bootstraps the application, loads configuration,
 * registers autoloaders, and dispatches the request to the correct controller.
 * ============================================================================
 */

declare(strict_types=1);

// 1. Define root path and start timer
define('ROOT_PATH', __DIR__);
define('START_TIME', microtime(true));

// 2. Load Composer autoloader (PSR-4 + helpers) when available.
//    If Composer hasn't been installed, fall back to a manual autoloader
//    that mirrors the namespace -> folder mapping defined in composer.json.
if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require ROOT_PATH . '/vendor/autoload.php';
} else {
    // PSR-4 mapping: namespace prefix -> base directory
    $namespaceMap = [
        'App\\Core\\'        => ROOT_PATH . '/core/',
        'App\\Controllers\\' => ROOT_PATH . '/controllers/',
        'App\\Models\\'      => ROOT_PATH . '/models/',
    ];

    spl_autoload_register(function (string $class) use ($namespaceMap): void {
        foreach ($namespaceMap as $prefix => $baseDir) {
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) continue;
            $relative = substr($class, $len);
            $file     = $baseDir . str_replace('\\', '/', $relative) . '.php';
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    });

    // Load global helpers manually (Composer's autoload.files isn't running)
    if (file_exists(ROOT_PATH . '/core/helpers.php')) {
        require ROOT_PATH . '/core/helpers.php';
    }
}

// 3. Load environment variables from .env (Dotenv)
if (class_exists('Dotenv\\Dotenv') && file_exists(ROOT_PATH . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
    $dotenv->load();
    $dotenv->required(['APP_NAME', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME']);
} else {
    // Fallback: load .env manually as $_ENV / $_SERVER entries
    if (file_exists(ROOT_PATH . '/.env')) {
        $lines = file(ROOT_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) continue;
            if (!str_contains($line, '=')) continue;
            [$key, $value] = array_map('trim', explode('=', $line, 2));
            $value = trim($value, '"\'');
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

// 4. Load application configuration
$config = require ROOT_PATH . '/config/app.php';
date_default_timezone_set($config['app']['timezone'] ?? 'Africa/Lagos');

// 5. Error handling based on APP_DEBUG
if (($config['app']['debug'] ?? false) === true) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', ROOT_PATH . '/logs/error.log');
}

// 6. Ensure writable directories exist
foreach (['logs', 'uploads', 'uploads/events', 'uploads/sermons', 'uploads/gallery'] as $dir) {
    $full = ROOT_PATH . '/' . $dir;
    if (!is_dir($full)) @mkdir($full, 0755, true);
}

// 7. Start secure session
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', ($config['app']['env'] ?? 'production') === 'production' ? '1' : '0');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.gc_maxlifetime', (string)(($config['session']['lifetime'] ?? 30) * 60));
    session_start();
}

// 8. Regenerate session ID periodically to prevent fixation
if (!isset($_SESSION['_last_regeneration'])) {
    session_regenerate_id(true);
    $_SESSION['_last_regeneration'] = time();
} elseif (time() - $_SESSION['_last_regeneration'] > 300) {
    session_regenerate_id(true);
    $_SESSION['_last_regeneration'] = time();
}

// 9. Serve static assets directly (let .htaccess do this in production)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($uri !== '/' && file_exists(ROOT_PATH . $uri) && !is_dir(ROOT_PATH . $uri)) {
    return false; // let PHP's built-in server handle static files
}

// 10. Dispatch request through the router
try {
    $router = new \App\Core\Router($config);
    $router->dispatch($_SERVER['REQUEST_METHOD'], $uri);
} catch (\Throwable $e) {
    http_response_code(500);
    $msg = $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
    if (($config['app']['debug'] ?? false) === true) {
        echo '<h1>500 Server Error</h1>';
        echo '<pre>' . htmlspecialchars($msg) . "\n\n" . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        echo '<h1>500 Server Error</h1><p>Something went wrong. Please try again later.</p>';
    }
    @file_put_contents(
        ROOT_PATH . '/logs/error.log',
        '[' . date('Y-m-d H:i:s') . '] ' . $msg . "\n",
        FILE_APPEND
    );
}
