<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (!isset($this->routes[$method][$uri])) {
            Response::notFound();
            return;
        }

        [$controller, $action] = $this->routes[$method][$uri];

        $controllerInstance = new $controller();

        if (!method_exists($controllerInstance, $action)) {
            Response::notFound();
            return;
        }

        $controllerInstance->$action();
    }
}