<?php

declare(strict_types=1);

namespace App\Core;

final class Flash
{
    public static function zet(string $type, string $bericht): void
    {
        $_SESSION['flash'] = ['type' => $type, 'bericht' => $bericht];
    }

    public static function ophalenEnWissen(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }
}
