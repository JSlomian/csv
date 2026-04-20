<?php

declare(strict_types=1);

namespace Jslomian\Csv;

final class Router
{
    /**
     * @var array<string, array<string, callable|array>>
     */
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function dispatch(string $method, string $path): void
    {
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo '404 Not Found';

            return;
        }

        if (is_array($handler) && is_string($handler[0])) {
            [$class, $action] = $handler;
            $controller = new $class();
            $controller->$action();

            return;
        }

        call_user_func($handler);
    }
}
