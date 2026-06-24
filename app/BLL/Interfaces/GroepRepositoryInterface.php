<?php

declare(strict_types=1);

namespace App\BLL\Interfaces;

use App\BLL\Models\Groep;

interface GroepRepositoryInterface
{
    /** @return Groep[] */
    public function alle(): array;

    public function zoekOpId(int $id): ?Groep;

    /** @return Groep[] gekoppeld aan deze instructeur */
    public function vanInstructeur(int $instructeurId): array;
}
