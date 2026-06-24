<?php

declare(strict_types=1);

namespace App\BLL\Interfaces;

use App\BLL\Models\Groep;

interface GroepRepositoryInterface
{
    /** @return Groep[] */
    public function alle(): array;

    public function zoekOpId(int $id): ?Groep;

    /** @return Groep[] gekoppeld aan deze instructeur óf door deze gebruiker zelf aangemaakt */
    public function vanInstructeur(int $instructeurId): array;

    public function aanmaken(
        string $naam,
        int $afdelingId,
        \DateTimeImmutable $startDatum,
        ?\DateTimeImmutable $eindDatum,
        int $authId,
    ): int;

    public function bijwerken(
        int $id,
        string $naam,
        int $afdelingId,
        \DateTimeImmutable $startDatum,
        ?\DateTimeImmutable $eindDatum,
        int $authId,
    ): void;

    public function verwijderen(int $id, int $authId): void;

    public function actiefWijzigen(int $id, bool $actief, int $authId): void;

    /** @param int[] $gebruikerIds */
    public function instructeursInstellen(int $groepId, array $gebruikerIds, int $authId): void;
}
