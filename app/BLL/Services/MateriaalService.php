<?php

declare(strict_types=1);

namespace App\BLL\Services;

use App\BLL\Interfaces\MateriaalRepositoryInterface;
use App\BLL\Models\Materiaal;

final class MateriaalService
{
    public function __construct(
        private readonly MateriaalRepositoryInterface $materiaalRepository,
    ) {
    }

    /** @return Materiaal[] */
    public function alleMaterialen(): array
    {
        return $this->materiaalRepository->alle();
    }
}
