<?php

declare(strict_types=1);

namespace App\BLL\Interfaces;

use App\BLL\Models\Les;

interface LesRepositoryInterface
{
    /** @return Les[] */
    public function alle(): array;

    public function zoekOpId(int $id): ?Les;

    /** @return Les[] lessen zonder gekoppelde lesplanning (voor dashboard-waarschuwingen) */
    public function zonderLesplanning(): array;
}
