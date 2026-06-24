<?php

declare(strict_types=1);

namespace App\DAL\Fake;

use App\BLL\Interfaces\LesplanningRepositoryInterface;
use App\BLL\Models\Lesplanning;

final class FakeLesplanningRepository implements LesplanningRepositoryInterface
{
    public function alle(): array
    {
        return FakeData::lesplanningen();
    }

    public function zoekOpId(int $id): ?Lesplanning
    {
        foreach (FakeData::lesplanningen() as $lesplanning) {
            if ($lesplanning->id === $id) {
                return $lesplanning;
            }
        }
        return null;
    }
}
