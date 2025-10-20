<?php

namespace App\Routing;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $callback): void
    {
        $this->routes['GET'][$path] = $callback;
    }

    public function post(string $path, callable $callback): void
    {
        $this->routes['POST'][$path] = $callback;
    }

    public function dispatch(string $method, string $path): void
    {
        $callback = $this->routes[$method][$path] ?? null;

        if (!$callback) {
            http_response_code(404);
            echo json_encode(['error' => 'Not found']);
            return;
        }

        call_user_func($callback);
    }
}
