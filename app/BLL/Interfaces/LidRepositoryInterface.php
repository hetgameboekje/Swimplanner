<?php

declare(strict_types=1);

namespace App\BLL\Interfaces;

use App\BLL\Models\Lid;

interface LidRepositoryInterface
{
    /** @return Lid[] actieve leden (nog niet uitgeschreven) van deze groep */
    public function ledenVanGroep(int $groepId): array;

    public function zoekOpId(int $id): ?Lid;

    /** Maakt een lid aan en koppelt het direct aan de groep. */
    public function aanmakenEnKoppelen(
        string $voornaam,
        string $achternaam,
        ?int $geboortejaar,
        ?string $contactgegevens,
        int $groepId,
        int $authId,
    ): int;

    /** Schrijft een lid uit bij een groep (behoudt historie in groep_leden). */
    public function uitschrijven(int $groepId, int $lidId, int $authId): void;
}
