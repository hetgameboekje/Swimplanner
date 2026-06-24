<?php

declare(strict_types=1);

namespace App\BLL\Models;

final class Groep
{
    /** @param Gebruiker[] $instructeurs */
    public function __construct(
        public readonly int $id,
        public readonly string $naam,
        public readonly Afdeling $afdeling,
        public readonly array $instructeurs = [],
        public readonly int $aantalLeden = 0,
        public readonly bool $actief = true,
    ) {
    }
}
