<?php

declare(strict_types=1);

namespace App\BLL\Interfaces;

use App\BLL\Models\Materiaal;

interface MateriaalRepositoryInterface
{
    /** @return Materiaal[] */
    public function alle(): array;
}
