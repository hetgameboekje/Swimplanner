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

    /**
     * @param int[] $groepIds
     * @param int[] $instructeurIds
     */
    public function aanmaken(
        array $groepIds,
        \DateTimeImmutable $datum,
        string $type,
        array $instructeurIds,
        ?string $beginTijd,
        ?string $eindTijd,
        ?string $locatie,
        int $authId,
    ): int;

    /**
     * @param int[] $groepIds
     * @param int[] $instructeurIds
     */
    public function bijwerken(
        int $id,
        array $groepIds,
        \DateTimeImmutable $datum,
        string $type,
        array $instructeurIds,
        ?string $beginTijd,
        ?string $eindTijd,
        ?string $locatie,
        int $authId,
    ): void;

    public function verwijderen(int $id, int $authId): void;
}
