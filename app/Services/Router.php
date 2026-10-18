<?php
namespace App\Services;

/**
 * Router
 * 
 * Simple request router that maps HTTP method + URI pattern to controller actions.
 * Supports path parameters (e.g., /users/{id}) and route names.
 */
class Router
{
    private array $routes = [];
    private array $namedRoutes = [];
    private array $middleware = [];

    /**
     * Register a GET route.
     */
    public function get(string $path, $handler, ?string $name = null, array $middleware = []): self
    {
        return $this->addRoute('GET', $path, $handler, $name, $middleware);
    }

    /**
     * Register a POST route.
     */
    public function post(string $path, $handler, ?string $name = null, array $middleware = []): self
    {
        return $this->addRoute('POST', $path, $handler, $name, $middleware);
    }

    /**
     * Register a route for multiple methods.
     */
    public function match(array $methods, string $path, $handler, ?string $name = null, array $middleware = []): self
    {
        foreach ($methods as $method) {
            $this->addRoute($method, $path, $handler, $name, $middleware);
        }
        return $this;
    }

    /**
     * Register a route.
     */
    private function addRoute(string $method, string $path, $handler, ?string $name, array $middleware): self
    {
        $route = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'name' => $name,
            'middleware' => $middleware,
            'pattern' => $this->pathToPattern($path),
        ];
        $this->routes[] = $route;

        if ($name) {
            $this->namedRoutes[$name] = $route;
        }

        return $this;
    }

    /**
     * Convert a route path like /users/{id} to a regex pattern.
     */
    private function pathToPattern(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    /**
     * Dispatch the current request to a matching route.
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getRequestUri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Apply middleware
                foreach ($route['middleware'] as $mw) {
                    $this->applyMiddleware($mw);
                }

                // Call the handler
                $handler = $route['handler'];
                if (is_array($handler)) {
                    [$class, $method] = $handler;
                    $controller = new $class();
                    $controller->$method(...$params);
                } elseif (is_callable($handler)) {
                    $handler(...$params);
                }
                return;
            }
        }

        // No route matched
        http_response_code(404);
        $this->renderError(404, 'Page Not Found', 'The page you requested could not be found.');
    }

    /**
     * Get the current request URI without query string.
     */
    private function getRequestUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        // Remove the base path prefix if running in a subdirectory
        $config = require dirname(__DIR__, 2) . '/config/app.php';
        $basePath = parse_url($config['base_url'], PHP_URL_PATH);
        if ($basePath && $basePath !== '/') {
            $basePath = rtrim($basePath, '/');
            if (str_starts_with($uri, $basePath)) {
                $uri = substr($uri, strlen($basePath)) ?: '/';
            }
        }

        return $uri;
    }

    /**
     * Apply a middleware function.
     */
    private function applyMiddleware(string $middlewareName): void
    {
        $middlewareMap = [
            'auth' => \App\Middleware\AuthMiddleware::class,
            'admin' => \App\Middleware\AdminMiddleware::class,
            'guest' => \App\Middleware\GuestMiddleware::class,
            'funder' => \App\Middleware\FunderMiddleware::class,
            'staff' => \App\Middleware\StaffMiddleware::class,
        ];

        if (isset($middlewareMap[$middlewareName])) {
            $middlewareClass = $middlewareMap[$middlewareName];
            $middleware = new $middlewareClass();
            $middleware->handle();
        }
    }

    /**
     * Generate a URL for a named route.
     */
    public function url(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \RuntimeException("Route '{$name}' not found.");
        }

        $path = $this->namedRoutes[$name]['path'];
        foreach ($params as $key => $value) {
            $path = str_replace('{' . $key . '}', $value, $path);
        }

        return \App\Helpers\Url::base(ltrim($path, '/'));
    }

    /**
     * Render an error page.
     */
    private function renderError(int $code, string $title, string $message): void
    {
        $e = (object)['code' => $code, 'title' => $title, 'message' => $message];
        require dirname(__DIR__, 2) . '/resources/views/errors/index.php';
    }
}
