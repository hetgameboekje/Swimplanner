<?php

declare(strict_types=1);

namespace App\BLL\Interfaces;

use App\BLL\Models\Materiaal;

interface MateriaalRepositoryInterface
{
    /** @return Materiaal[] */
    public function alle(): array;

    public function zoekOpId(int $id): ?Materiaal;

    public function aanmaken(string $naam, ?string $categorie, int $authId): int;

    public function bijwerken(int $id, string $naam, ?string $categorie, int $authId): void;

    public function verwijderen(int $id, int $authId): void;

    public function actiefWijzigen(int $id, bool $actief, int $authId): void;
}
