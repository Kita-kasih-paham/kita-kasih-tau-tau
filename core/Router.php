<?php

namespace Core;

class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function get(string $path, callable|array $handler): self
    {
        return $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): self
    {
        return $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, callable|array $handler): self
    {
        return $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, callable|array $handler): self
    {
        return $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, callable|array $handler): self
    {
        $this->routes[] = [
            'method'      => $method,
            'path'        => $path,
            'handler'     => $handler,
            'middlewares' => [],
        ];
        return $this;
    }

    public function middleware(string|array $middleware): self
    {
        $last = array_key_last($this->routes);
        $middlewares = is_array($middleware) ? $middleware : [$middleware];
        $this->routes[$last]['middlewares'] = array_merge(
            $this->routes[$last]['middlewares'],
            $middlewares
        );
        return $this;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            $params = $this->matchPath($route['path'], $uri);
            if ($params === null) continue;

            // Run middlewares
            foreach ($route['middlewares'] as $mw) {
                (new $mw())->handle();
            }

            // Call handler
            $handler = $route['handler'];
            if (is_callable($handler)) {
                call_user_func($handler, $params);
            } elseif (is_array($handler)) {
                [$class, $method] = $handler;
                call_user_func([new $class(), $method], $params);
            }
            return;
        }

        http_response_code(404);
        require __DIR__ . '/../pages/404.php';
    }

    private function matchPath(string $routePath, string $uri): ?array
    {
        // Convert {param} to named capture groups
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $uri, $matches)) {
            return null;
        }

        // Return only named params
        return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
    }
}
