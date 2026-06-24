<?php

declare(strict_types=1);

namespace App\DAL;

/**
 * Eén PDO-connectie voor de hele applicatie (runtime). Voor het opbouwen
 * van het schema zelf (database aanmaken, tabellen creëren) gebruikt
 * database/build.php een eigen connectie, omdat de database op dat moment
 * nog niet hoeft te bestaan.
 */
final class Database
{
    private static ?\PDO $connectie = null;

    public static function connectie(): \PDO
    {
        if (self::$connectie !== null) {
            return self::$connectie;
        }

        $config = require __DIR__ . '/../../config/database.php';

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset'],
        );

        return self::$connectie = new \PDO($dsn, $config['username'], $config['password'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
}
