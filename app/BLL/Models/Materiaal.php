<?php

declare(strict_types=1);

namespace App\BLL\Models;

final class Materiaal
{
    public function __construct(
        public readonly int $id,
        public readonly string $naam,
        public readonly string $categorie,
        public readonly bool $actief = true,
    ) {
    }
}
