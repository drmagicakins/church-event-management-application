<?php
/**
 * ============================================================================
 * Team Praise Official - Application Configuration
 * ============================================================================
 * Central configuration file. Reads values from the .env file and provides
 * defaults so the rest of the codebase never touches $_ENV directly.
 * ============================================================================
 */

declare(strict_types=1);

return [
    'app' => [
        'name'     => $_ENV['APP_NAME']     ?? 'Team Praise Official',
        'env'      => $_ENV['APP_ENV']      ?? 'production',
        'debug'    => filter_var($_ENV['APP_DEBUG'] ?? 'false', FILTER_VALIDATE_BOOLEAN),
        'url'      => $_ENV['APP_URL']      ?? 'http://localhost',
        'key'      => $_ENV['APP_KEY']      ?? 'change-me-please',
        'timezone' => $_ENV['APP_TIMEZONE'] ?? 'Africa/Lagos',
    ],

    'database' => [
        'host'     => $_ENV['DB_HOST']     ?? 'localhost',
        'port'     => (int)($_ENV['DB_PORT'] ?? 3306),
        'name'     => $_ENV['DB_DATABASE'] ?? 'teampraise_app',
        'user'     => $_ENV['DB_USERNAME'] ?? 'root',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
        'charset'  => $_ENV['DB_CHARSET']  ?? 'utf8mb4',
    ],

    'mail' => [
        'host'       => $_ENV['MAIL_HOST']       ?? 'smtp.example.com',
        'port'       => (int)($_ENV['MAIL_PORT'] ?? 587),
        'username'   => $_ENV['MAIL_USERNAME']   ?? '',
        'password'   => $_ENV['MAIL_PASSWORD']   ?? '',
        'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
        'from_email' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'no-reply@example.com',
        'from_name'  => $_ENV['MAIL_FROM_NAME']    ?? 'Team Praise Official',
    ],

    'upload' => [
        'max_mb'        => (int)($_ENV['UPLOAD_MAX_MB'] ?? 8),
        'allowed_ext'   => explode(',', $_ENV['UPLOAD_ALLOWED_EXT'] ?? 'jpg,jpeg,png,webp,gif,mp4,mp3,pdf'),
        'destination'   => ROOT_PATH . '/uploads',
    ],

    'session' => [
        'lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 30),
        'secure'   => filter_var($_ENV['SESSION_SECURE'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
        'httponly' => filter_var($_ENV['SESSION_HTTPONLY'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
        'samesite' => $_ENV['SESSION_SAMESITE'] ?? 'Lax',
    ],

    'rate_limit' => [
        'login' => (int)($_ENV['RATE_LIMIT_LOGIN'] ?? 5),
        'form'  => (int)($_ENV['RATE_LIMIT_FORM']  ?? 20),
    ],

    'cache' => [
        'driver' => $_ENV['CACHE_DRIVER'] ?? 'file',
        'ttl'    => (int)($_ENV['CACHE_TTL'] ?? 3600),
        'path'   => ROOT_PATH . '/storage/cache',
    ],

    'paths' => [
        'views'      => ROOT_PATH . '/views',
        'uploads'    => ROOT_PATH . '/uploads',
        'logs'       => ROOT_PATH . '/logs',
        'controllers'=> ROOT_PATH . '/controllers',
        'models'     => ROOT_PATH . '/models',
    ],
];
