<?php

declare(strict_types=1);

namespace App\BLL\Models;

enum AanwezigheidStatus: string
{
    case Aanwezig = 'aanwezig';
    case Afwezig = 'afwezig';
    case Afgemeld = 'afgemeld';
    case TeLaat = 'te_laat';

    public function label(): string
    {
        return match ($this) {
            self::Aanwezig => 'Aanwezig',
            self::Afwezig => 'Afwezig',
            self::Afgemeld => 'Afgemeld',
            self::TeLaat => 'Te laat',
        };
    }

    /** Korte code voor compacte matrixweergaves (aanwezigheidsoverzicht). */
    public function kort(): string
    {
        return match ($this) {
            self::Aanwezig => 'A',
            self::Afwezig => 'Af',
            self::Afgemeld => 'Agm',
            self::TeLaat => 'TL',
        };
    }

    /** Bootstrap badge-kleurklasse, voor consistente kleurcodering in views. */
    public function badgeKleur(): string
    {
        return match ($this) {
            self::Aanwezig => 'success',
            self::Afwezig => 'danger',
            self::Afgemeld => 'secondary',
            self::TeLaat => 'warning',
        };
    }
}
