<?php

declare(strict_types=1);

namespace App\BLL\Interfaces;

use App\BLL\Models\Lesplanning;

interface LesplanningRepositoryInterface
{
    /** @return Lesplanning[] */
    public function alle(): array;

    public function zoekOpId(int $id): ?Lesplanning;

    /**
     * @param array<int, array{naam: string, tijdIndicatie: ?string, doel: ?string, activiteit: ?string, organisatieEnMaterialen: ?string, didactischeAanwijzingen: ?string, materiaalIds: int[]}> $onderdelen
     */
    public function aanmaken(
        int $groepId,
        int $instructeurId,
        ?int $lesId,
        \DateTimeImmutable $datum,
        string $beginTijd,
        string $eindTijd,
        ?string $locatie,
        ?string $beginsituatie,
        ?string $doelstelling,
        array $onderdelen,
        int $authId,
    ): int;

    /**
     * @param array<int, array{naam: string, tijdIndicatie: ?string, doel: ?string, activiteit: ?string, organisatieEnMaterialen: ?string, didactischeAanwijzingen: ?string, materiaalIds: int[]}> $onderdelen
     */
    public function bijwerken(
        int $id,
        int $groepId,
        int $instructeurId,
        ?int $lesId,
        \DateTimeImmutable $datum,
        string $beginTijd,
        string $eindTijd,
        ?string $locatie,
        ?string $beginsituatie,
        ?string $doelstelling,
        array $onderdelen,
        int $authId,
    ): void;

    public function verwijderen(int $id, int $authId): void;
}
