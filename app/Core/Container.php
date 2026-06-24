<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Minimale dependency-container: koppelt interfaces aan implementaties.
 * Vandaag wijzen alle interfaces naar de Fake-DAL; zodra de echte PDO-DAL
 * er is, verandert alleen dit bestand — BLL en Presentation blijven gelijk.
 */
final class Container
{
    /** @var array<class-string, \Closure> */
    private static array $bindings = [];

    public static function bind(string $interface, \Closure $factory): void
    {
        self::$bindings[$interface] = $factory;
    }

    public static function maak(string $interface): object
    {
        if (!isset(self::$bindings[$interface])) {
            throw new \RuntimeException("Geen binding geregistreerd voor {$interface}");
        }
        return (self::$bindings[$interface])();
    }
}
