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
}
