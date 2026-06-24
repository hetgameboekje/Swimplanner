<?php

declare(strict_types=1);

namespace App\DAL\Fake;

use App\BLL\Interfaces\GroepRepositoryInterface;
use App\BLL\Models\Groep;

final class FakeGroepRepository implements GroepRepositoryInterface
{
    public function alle(): array
    {
        return FakeData::groepen();
    }

    public function zoekOpId(int $id): ?Groep
    {
        foreach (FakeData::groepen() as $groep) {
            if ($groep->id === $id) {
                return $groep;
            }
        }
        return null;
    }

    public function vanInstructeur(int $instructeurId): array
    {
        return array_values(array_filter(
            FakeData::groepen(),
            static function (Groep $groep) use ($instructeurId): bool {
                foreach ($groep->instructeurs as $instructeur) {
                    if ($instructeur->id === $instructeurId) {
                        return true;
                    }
                }
                return false;
            },
        ));
    }
}
