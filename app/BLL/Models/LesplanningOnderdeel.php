<?php

declare(strict_types=1);

namespace App\BLL\Models;

final class LesplanningOnderdeel
{
    /** @param Materiaal[] $materialen */
    public function __construct(
        public readonly int $volgnummer,
        public readonly string $naam,
        public readonly string $tijdIndicatie,
        public readonly string $doel,
        public readonly string $activiteit,
        public readonly string $organisatieEnMaterialen,
        public readonly string $didactischeAanwijzingen,
        public readonly array $materialen = [],
    ) {
    }
}
