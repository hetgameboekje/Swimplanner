<?php

declare(strict_types=1);

namespace App\BLL\Models;

enum LesType: string
{
    case Regulier = 'regulier';
    case Activiteit = 'activiteit';
    case Examen = 'examen';
}

final class Les
{
    public function __construct(
        public readonly int $id,
        public readonly Groep $groep,
        public readonly \DateTimeImmutable $datum,
        public readonly LesType $type,
        public readonly Gebruiker $instructeur,
        public readonly bool $heeftLesplanning = false,
    ) {
    }

    public function isOpZondag(): bool
    {
        return (int) $this->datum->format('N') === 7;
    }
}
