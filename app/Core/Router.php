<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Minimale route-dispatcher: methode + pad -> [Controller::class, 'actie'].
 * Ondersteunt dynamische segmenten zoals "/groepen/{id}/bewerken"; de
 * waarden worden in volgorde als argumenten aan de controller-actie
 * meegegeven. Geen externe dependency; bewust klein gehouden zodat het
 * gedrag volledig inzichtelijk blijft.
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

        $route = $this->vindRoute($methode, $pad);

        if ($route === null) {
            http_response_code(404);
            echo '404 - Pagina niet gevonden';
            return;
        }

        [$controllerClass, $actie, $parameters] = $route;
        $controller = new $controllerClass();
        $controller->$actie(...$parameters);
    }

    /**
     * @return array{0: class-string, 1: string, 2: list<string>}|null
     */
    private function vindRoute(string $methode, string $pad): ?array
    {
        $routesVoorMethode = $this->routes[$methode] ?? [];

        if (isset($routesVoorMethode[$pad])) {
            [$controller, $actie] = $routesVoorMethode[$pad];
            return [$controller, $actie, []];
        }

        foreach ($routesVoorMethode as $patroon => $route) {
            if (!str_contains($patroon, '{')) {
                continue;
            }

            $regex = '#^' . preg_replace('#\{\w+\}#', '([^/]+)', $patroon) . '$#';
            if (preg_match($regex, $pad, $matches)) {
                [$controller, $actie] = $route;
                return [$controller, $actie, array_slice($matches, 1)];
            }
        }

        return null;
    }
}
