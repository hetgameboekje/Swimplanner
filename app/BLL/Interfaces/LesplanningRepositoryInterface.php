<?php

declare(strict_types=1);

namespace App\BLL\Interfaces;

use App\BLL\Models\Lesplanning;

interface LesplanningRepositoryInterface
{
    /** @return Lesplanning[] */
    public function alle(): array;

    public function zoekOpId(int $id): ?Lesplanning;
}
