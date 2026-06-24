<?php

declare(strict_types=1);

namespace App\DAL\Pdo;

use App\BLL\Interfaces\AfdelingRepositoryInterface;
use App\BLL\Models\Afdeling;
use App\DAL\Database;

final class PdoAfdelingRepository implements AfdelingRepositoryInterface
{
    public function alleActief(): array
    {
        $rijen = Database::connectie()
            ->query('SELECT * FROM afdelingen WHERE actief = 1 ORDER BY naam')
            ->fetchAll();

        return array_map($this->rijNaarAfdeling(...), $rijen);
    }

    private function rijNaarAfdeling(array $rij): Afdeling
    {
        return new Afdeling((int) $rij['id'], $rij['naam'], (bool) $rij['actief']);
    }
}
