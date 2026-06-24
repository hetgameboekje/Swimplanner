<?php

declare(strict_types=1);

namespace App\BLL\Services;

use App\BLL\Interfaces\LesRepositoryInterface;
use App\BLL\Models\Les;
use App\BLL\Models\LesType;

final class LesService
{
    public function __construct(
        private readonly LesRepositoryInterface $lesRepository,
    ) {
    }

    /** @return Les[] */
    public function alleLessen(): array
    {
        return $this->lesRepository->alle();
    }

    public function zoekOpId(int $id): ?Les
    {
        return $this->lesRepository->zoekOpId($id);
    }

    /**
     * @param int[] $groepIds
     * @param int[] $instructeurIds
     */
    public function aanmaken(
        array $groepIds,
        string $datum,
        string $type,
        array $instructeurIds,
        ?string $beginTijd,
        ?string $eindTijd,
        int $authId,
    ): int {
        $groepIds = $this->valideerIds($groepIds, 'Kies minimaal één groep.');
        $instructeurIds = $this->valideerIds($instructeurIds, 'Kies minimaal één instructeur.');
        $datumObject = $this->valideerDatum($datum);
        $lesType = $this->valideerType($type);
        $this->valideerTijden($beginTijd, $eindTijd);

        return $this->lesRepository->aanmaken($groepIds, $datumObject, $lesType->value, $instructeurIds, $beginTijd, $eindTijd, $authId);
    }

    /**
     * @param int[] $groepIds
     * @param int[] $instructeurIds
     */
    public function bijwerken(
        int $id,
        array $groepIds,
        string $datum,
        string $type,
        array $instructeurIds,
        ?string $beginTijd,
        ?string $eindTijd,
        int $authId,
    ): void {
        $groepIds = $this->valideerIds($groepIds, 'Kies minimaal één groep.');
        $instructeurIds = $this->valideerIds($instructeurIds, 'Kies minimaal één instructeur.');
        $datumObject = $this->valideerDatum($datum);
        $lesType = $this->valideerType($type);
        $this->valideerTijden($beginTijd, $eindTijd);

        $this->lesRepository->bijwerken($id, $groepIds, $datumObject, $lesType->value, $instructeurIds, $beginTijd, $eindTijd, $authId);
    }

    public function verwijderen(int $id, int $authId): void
    {
        $this->lesRepository->verwijderen($id, $authId);
    }

    /**
     * @param int[] $ids
     * @return int[]
     */
    private function valideerIds(array $ids, string $foutmelding): array
    {
        $ids = array_values(array_unique(array_filter($ids, static fn (int $id) => $id > 0)));
        if (empty($ids)) {
            throw new \InvalidArgumentException($foutmelding);
        }
        return $ids;
    }

    private function valideerDatum(string $datum): \DateTimeImmutable
    {
        $object = \DateTimeImmutable::createFromFormat('Y-m-d', $datum);
        if ($object === false) {
            throw new \InvalidArgumentException('Ongeldige datum.');
        }
        return $object;
    }

    private function valideerType(string $type): LesType
    {
        $gevonden = LesType::tryFrom($type);
        if ($gevonden === null) {
            throw new \InvalidArgumentException('Ongeldig lestype.');
        }
        return $gevonden;
    }

    private function valideerTijden(?string $beginTijd, ?string $eindTijd): void
    {
        if ($beginTijd !== null && $beginTijd !== '' && $eindTijd !== null && $eindTijd !== '' && $beginTijd >= $eindTijd) {
            throw new \InvalidArgumentException('Eindtijd moet na begintijd liggen.');
        }
    }
}
