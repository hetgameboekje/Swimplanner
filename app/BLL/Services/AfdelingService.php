<?php

declare(strict_types=1);

namespace App\BLL\Services;

use App\BLL\Interfaces\AfdelingRepositoryInterface;
use App\BLL\Models\Afdeling;

final class AfdelingService
{
    public function __construct(
        private readonly AfdelingRepositoryInterface $afdelingRepository,
    ) {
    }

    /** @return Afdeling[] */
    public function alleActief(): array
    {
        return $this->afdelingRepository->alleActief();
    }
}
