<?php

declare(strict_types=1);

namespace App\BLL\Models;

final class Lesplanning
{
    /** @param LesplanningOnderdeel[] $onderdelen */
    public function __construct(
        public readonly int $id,
        public readonly Groep $groep,
        public readonly Gebruiker $instructeur,
        public readonly \DateTimeImmutable $datum,
        public readonly string $beginTijd,
        public readonly string $eindTijd,
        public readonly string $locatie,
        public readonly string $beginsituatie,
        public readonly string $doelstelling,
        public readonly array $onderdelen = [],
        public readonly ?int $lesId = null,
    ) {
    }

    /**
     * Standaard lesopbouw bij aanmaak: 4 onderdelen, instructeur kan
     * later extra Kern-onderdelen toevoegen.
     */
    public static function standaardOnderdeelNamen(): array
    {
        return ['Inleiding', 'Kern 1', 'Kern 2', 'Afsluiting'];
    }
}
