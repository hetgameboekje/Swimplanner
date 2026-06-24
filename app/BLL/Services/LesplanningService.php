<?php

declare(strict_types=1);

namespace App\BLL\Services;

use App\BLL\Interfaces\LesplanningRepositoryInterface;
use App\BLL\Models\Lesplanning;

final class LesplanningService
{
    public function __construct(
        private readonly LesplanningRepositoryInterface $lesplanningRepository,
    ) {
    }

    /** @return Lesplanning[] */
    public function alleLesplanningen(): array
    {
        return $this->lesplanningRepository->alle();
    }

    public function zoekOpId(int $id): ?Lesplanning
    {
        return $this->lesplanningRepository->zoekOpId($id);
    }
}
