<?php

declare(strict_types=1);

namespace App\BLL\Services;

use App\BLL\Interfaces\AanwezigheidRepositoryInterface;
use App\BLL\Models\AanwezigheidRegel;
use App\BLL\Models\AanwezigheidStatus;

final class AanwezigheidService
{
    public function __construct(
        private readonly AanwezigheidRepositoryInterface $aanwezigheidRepository,
    ) {
    }

    /** @return AanwezigheidRegel[] */
    public function voorLes(int $lesId): array
    {
        return $this->aanwezigheidRepository->vanLes($lesId);
    }

    /**
     * @param array<int|string, array{status?: string, opmerking?: string}> $ruweRegels keyed op lid_id
     */
    public function opslaan(int $lesId, array $ruweRegels, int $authId): void
    {
        $regels = [];

        foreach ($ruweRegels as $lidId => $regel) {
            $status = AanwezigheidStatus::tryFrom((string) ($regel['status'] ?? ''));
            if ($status === null) {
                throw new \InvalidArgumentException('Ongeldige aanwezigheidsstatus.');
            }

            $opmerking = trim((string) ($regel['opmerking'] ?? ''));

            $regels[(int) $lidId] = [
                'status' => $status->value,
                'opmerking' => $opmerking === '' ? null : $opmerking,
            ];
        }

        if (empty($regels)) {
            throw new \InvalidArgumentException('Geen leden om aanwezigheid voor te registreren.');
        }

        $this->aanwezigheidRepository->opslaan($lesId, $regels, $authId);
    }
}
