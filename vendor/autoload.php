<?php

declare(strict_types=1);

/**
 * Minimale PSR-4 autoloader voor de "App\" namespace, zonder afhankelijkheid
 * van een geïnstalleerde Composer. Zodra `composer install` lokaal (Laragon)
 * gedraaid wordt, overschrijft Composer dit bestand met de echte autoloader
 * op basis van composer.json — er verandert dan niets aan de projectcode.
 */
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relatief = substr($class, strlen($prefix));
    $pad = __DIR__ . '/../app/' . str_replace('\\', '/', $relatief) . '.php';

    if (is_file($pad)) {
        require $pad;
    }
});
