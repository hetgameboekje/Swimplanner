<?php

declare(strict_types=1);

namespace App\BLL\Models;

final class Les
{
    /**
     * @param Groep[] $groepen Eén les kan tegelijk voor meerdere groepen
     *     gegeven worden (bv. 2 groepen die samen 1 uur van een 3-urig
     *     blok draaien).
     * @param Gebruiker[] $instructeurs
     */
    public function __construct(
        public readonly int $id,
        public readonly array $groepen,
        public readonly \DateTimeImmutable $datum,
        public readonly LesType $type,
        public readonly array $instructeurs,
        public readonly ?string $beginTijd = null,
        public readonly ?string $eindTijd = null,
        public readonly bool $heeftLesplanning = false,
    ) {
    }
}
