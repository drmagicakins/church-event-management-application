<?php
/**
 * ============================================================================
 * Router - Simple front-controller router
 * ============================================================================
 * Maps URLs to controller actions. Supports:
 *   - Static paths:  /admin/login
 *   - Parameterized: /events/{id}
 *   - Method routing (GET, POST, PUT, DELETE)
 *   - Middleware stacks (auth, csrf, guest)
 * ============================================================================
 */

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $config;
    private array $routes = [];

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->registerRoutes();
    }

    /** Register every application route in one place. */
    private function registerRoutes(): void
    {
        // ---- Public frontend ----
        $this->add('GET',  '/',               'HomeController@index');
        $this->add('GET',  '/about',          'HomeController@about');
        $this->add('GET',  '/events',         'EventController@index');
        $this->add('GET',  '/events/{slug}',  'EventController@show');
        $this->add('POST', '/events/{id}/register', 'EventController@register');
        $this->add('GET',  '/sermons',        'SermonController@index');
        $this->add('GET',  '/blog',           'BlogController@index');
        $this->add('GET',  '/blog/{slug}',    'BlogController@show');
        $this->add('GET',  '/gallery',        'GalleryController@index');
        $this->add('GET',  '/contact',        'ContactController@index');
        $this->add('POST', '/contact',        'ContactController@store');
        $this->add('GET',  '/donation',       'DonationController@index');
        $this->add('POST', '/donation',       'DonationController@store');
        $this->add('GET',  '/prayer',         'PrayerController@index');
        $this->add('POST', '/prayer',         'PrayerController@store');
        $this->add('GET',  '/livestream',     'HomeController@livestream');
        $this->add('POST', '/newsletter',     'NewsletterController@subscribe');

        // ---- Admin authentication ----
        $this->add('GET',  '/admin/login',    'AuthController@loginForm',   ['guest']);
        $this->add('POST', '/admin/login',    'AuthController@login',       ['guest', 'csrf']);
        $this->add('POST', '/admin/logout',   'AuthController@logout',      ['auth']);
        $this->add('GET',  '/admin/forgot',   'AuthController@forgotForm',  ['guest']);
        $this->add('POST', '/admin/forgot',   'AuthController@forgot',      ['guest', 'csrf']);

        // ---- Admin dashboard (auth required) ----
        $this->add('GET',  '/admin',                   'DashboardController@index',   ['auth']);
        $this->add('GET',  '/admin/dashboard',         'DashboardController@index',   ['auth']);
        $this->add('GET',  '/admin/events',            'EventController@adminIndex',  ['auth']);
        $this->add('POST', '/admin/events',            'EventController@store',       ['auth', 'csrf']);
        $this->add('POST', '/admin/events/{id}',       'EventController@update',      ['auth', 'csrf']);
        $this->add('POST', '/admin/events/{id}/delete','EventController@destroy',     ['auth', 'csrf']);
        $this->add('GET',  '/admin/sermons',           'SermonController@adminIndex', ['auth']);
        $this->add('POST', '/admin/sermons',           'SermonController@store',      ['auth', 'csrf']);
        $this->add('GET',  '/admin/blog',              'BlogController@adminIndex',   ['auth']);
        $this->add('POST', '/admin/blog',              'BlogController@store',        ['auth', 'csrf']);
        $this->add('GET',  '/admin/donations',         'DonationController@adminIndex', ['auth']);
        $this->add('GET',  '/admin/contacts',          'ContactController@adminIndex',  ['auth']);
        $this->add('POST', '/admin/contacts/{id}/reply','ContactController@reply',      ['auth', 'csrf']);
        $this->add('GET',  '/admin/prayers',           'PrayerController@adminIndex',   ['auth']);
        $this->add('GET',  '/admin/subscribers',       'NewsletterController@adminIndex', ['auth']);
        $this->add('GET',  '/admin/users',             'UserController@index',           ['auth']);
        $this->add('GET',  '/admin/settings',          'SettingsController@index',       ['auth']);
        $this->add('POST', '/admin/settings',          'SettingsController@update',      ['auth', 'csrf']);

        // ---- REST API v1 ----
        $this->add('POST', '/api/v1/auth/login',   'Api\\AuthController@login');
        $this->add('GET',  '/api/v1/events',       'Api\\EventController@index');
        $this->add('GET',  '/api/v1/events/{id}',  'Api\\EventController@show');
        $this->add('GET',  '/api/v1/sermons',      'Api\\SermonController@index');
        $this->add('GET',  '/api/v1/blog-posts',   'Api\\BlogController@index');
        $this->add('POST', '/api/v1/donations',    'Api\\DonationController@store');
        $this->add('POST', '/api/v1/prayer-requests', 'Api\\PrayerController@store');
    }

    /** Register a single route. */
    private function add(string $method, string $path, string $handler, array $middleware = []): void
    {
        $this->routes[] = [
            'method'     => $method,
            'path'       => $path,
            'handler'    => $handler,
            'middleware' => $middleware,
            'pattern'    => $this->compilePattern($path),
        ];
    }

    /** Convert "/events/{id}" into a regex pattern. */
    private function compilePattern(string $path): string
    {
        $pattern = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    /** Dispatch the current request to the matching controller action. */
    public function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;
            if (!preg_match($route['pattern'], $uri, $matches)) continue;

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            // Run middleware stack
            foreach ($route['middleware'] as $mw) {
                $this->runMiddleware($mw);
            }

            // Resolve controller@action
            [$controllerName, $action] = explode('@', $route['handler']);
            $fqcn = 'App\\Controllers\\' . $controllerName;
            if (!class_exists($fqcn)) {
                throw new \RuntimeException("Controller not found: $fqcn");
            }
            $controller = new $fqcn($this->config);
            $controller->$action(...array_values($params));
            return;
        }

        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>';
    }

    /** Run a single middleware by name. */
    private function runMiddleware(string $name): void
    {
        switch ($name) {
            case 'auth':
                if (empty($_SESSION['admin_id'])) {
                    header('Location: /admin/login');
                    exit;
                }
                break;
            case 'guest':
                if (!empty($_SESSION['admin_id'])) {
                    header('Location: /admin/dashboard');
                    exit;
                }
                break;
            case 'csrf':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    Csrf::validate($_POST['csrf_token'] ?? '');
                }
                break;
        }
    }
}
