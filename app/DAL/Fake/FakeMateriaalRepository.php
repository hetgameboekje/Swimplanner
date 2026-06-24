<?php

declare(strict_types=1);

namespace App\DAL\Fake;

use App\BLL\Interfaces\MateriaalRepositoryInterface;

final class FakeMateriaalRepository implements MateriaalRepositoryInterface
{
    public function alle(): array
    {
        return FakeData::materialen();
    }
}
