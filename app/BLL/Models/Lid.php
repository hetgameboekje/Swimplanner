<?php

declare(strict_types=1);

namespace App\BLL\Models;

final class Lid
{
    public function __construct(
        public readonly int $id,
        public readonly string $voornaam,
        public readonly string $achternaam,
        public readonly ?int $geboortejaar = null,
        public readonly ?string $contactgegevens = null,
        public readonly bool $actief = true,
    ) {
    }

    public function volledigeNaam(): string
    {
        return trim("{$this->voornaam} {$this->achternaam}");
    }
}
