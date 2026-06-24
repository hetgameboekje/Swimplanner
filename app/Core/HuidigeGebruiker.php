<?php

declare(strict_types=1);

namespace App\Core;

use App\BLL\Models\Gebruiker;
use App\BLL\Models\Rol;

/**
 * Sessie-gebaseerde "ingelogde gebruiker". Eén centrale plek voor AUTHID,
 * zodat de rest van de applicatie (controllers, repositories) nooit zelf
 * met $_SESSION werkt.
 */
final class HuidigeGebruiker
{
    public static function isIngelogd(): bool
    {
        return isset($_SESSION['gebruiker_id']);
    }

    public static function id(): int
    {
        if (!self::isIngelogd()) {
            throw new \RuntimeException('Geen ingelogde gebruiker — controleer de route-protectie.');
        }
        return (int) $_SESSION['gebruiker_id'];
    }

    public static function naam(): string
    {
        return $_SESSION['gebruiker_naam'] ?? '';
    }

    public static function rol(): ?Rol
    {
        return isset($_SESSION['gebruiker_rol']) ? Rol::from($_SESSION['gebruiker_rol']) : null;
    }

    public static function isBeheerder(): bool
    {
        return self::rol() === Rol::Beheerder;
    }

    public static function inloggen(Gebruiker $gebruiker): void
    {
        session_regenerate_id(true);
        $_SESSION['gebruiker_id'] = $gebruiker->id;
        $_SESSION['gebruiker_naam'] = $gebruiker->naam;
        $_SESSION['gebruiker_rol'] = $gebruiker->rol->value;
    }

    public static function uitloggen(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
    }
}
