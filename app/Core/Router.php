<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Minimale route-dispatcher: methode + pad -> [Controller::class, 'actie'].
 * Geen externe dependency; bewust klein gehouden zodat het gedrag volledig
 * inzichtelijk blijft.
 */
final class Router
{
    /** @var array<string, array<string, array{0: class-string, 1: string}>> */
    private array $routes = [];

    public function get(string $pad, string $controller, string $actie): void
    {
        $this->routes['GET'][$pad] = [$controller, $actie];
    }

    public function post(string $pad, string $controller, string $actie): void
    {
        $this->routes['POST'][$pad] = [$controller, $actie];
    }

    public function dispatch(string $methode, string $pad): void
    {
        $pad = rtrim($pad, '/');
        if ($pad === '') {
            $pad = '/';
        }

        $route = $this->routes[$methode][$pad] ?? null;

        if ($route === null) {
            http_response_code(404);
            echo '404 - Pagina niet gevonden';
            return;
        }

        [$controllerClass, $actie] = $route;
        $controller = new $controllerClass();
        $controller->$actie();
    }
}
