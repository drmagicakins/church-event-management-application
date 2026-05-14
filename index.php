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

// 2. Load Composer autoloader (PSR-4 + helpers)
if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require ROOT_PATH . '/vendor/autoload.php';
} else {
    // Fallback manual autoload for environments without Composer
    spl_autoload_register(function (string $class): void {
        $path = ROOT_PATH . '/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($path)) require $path;
    });
    if (file_exists(ROOT_PATH . '/core/helpers.php')) {
        require ROOT_PATH . '/core/helpers.php';
    }
}

// 3. Load environment variables from .env (Dotenv)
if (class_exists('Dotenv\\Dotenv') && file_exists(ROOT_PATH . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
    $dotenv->load();
    $dotenv->required(['APP_NAME', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME']);
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

// 6. Start secure session
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', ($config['app']['env'] ?? 'production') === 'production' ? '1' : '0');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.gc_maxlifetime', (string)(($config['session']['lifetime'] ?? 30) * 60));
    session_start();
}

// 7. Regenerate session ID periodically to prevent fixation
if (!isset($_SESSION['_last_regeneration'])) {
    session_regenerate_id(true);
    $_SESSION['_last_regeneration'] = time();
} elseif (time() - $_SESSION['_last_regeneration'] > 300) {
    session_regenerate_id(true);
    $_SESSION['_last_regeneration'] = time();
}

// 8. Dispatch request through the router
try {
    $router = new \App\Core\Router($config);
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (\Throwable $e) {
    http_response_code(500);
    if (($config['app']['debug'] ?? false) === true) {
        echo '<h1>500 Server Error</h1><pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    } else {
        echo '<h1>500 Server Error</h1><p>Something went wrong. Please try again later.</p>';
        error_log('[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n", 3, ROOT_PATH . '/logs/error.log');
    }
}
