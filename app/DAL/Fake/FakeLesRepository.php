<?php

declare(strict_types=1);

namespace App\DAL\Fake;

use App\BLL\Interfaces\LesRepositoryInterface;
use App\BLL\Models\Les;

final class FakeLesRepository implements LesRepositoryInterface
{
    public function alle(): array
    {
        return FakeData::lessen();
    }

    public function zoekOpId(int $id): ?Les
    {
        foreach (FakeData::lessen() as $les) {
            if ($les->id === $id) {
                return $les;
            }
        }
        return null;
    }

    public function zonderLesplanning(): array
    {
        return array_values(array_filter(
            FakeData::lessen(),
            static fn (Les $les) => !$les->heeftLesplanning,
        ));
    }
}
