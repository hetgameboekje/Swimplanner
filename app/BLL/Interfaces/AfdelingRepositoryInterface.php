<?php

declare(strict_types=1);

namespace App\BLL\Interfaces;

use App\BLL\Models\Afdeling;

interface AfdelingRepositoryInterface
{
    /** @return Afdeling[] */
    public function alleActief(): array;
}
