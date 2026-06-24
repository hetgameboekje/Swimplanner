<?php

declare(strict_types=1);

namespace App\BLL\Services;

use App\BLL\Interfaces\GroepRepositoryInterface;
use App\BLL\Models\Groep;

final class GroepService
{
    public function __construct(
        private readonly GroepRepositoryInterface $groepRepository,
    ) {
    }

    /** @return Groep[] */
    public function alleGroepen(): array
    {
        return $this->groepRepository->alle();
    }

    public function zoekOpId(int $id): ?Groep
    {
        return $this->groepRepository->zoekOpId($id);
    }
}
