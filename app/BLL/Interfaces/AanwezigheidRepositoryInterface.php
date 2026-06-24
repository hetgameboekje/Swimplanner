<?php

declare(strict_types=1);

namespace App\BLL\Interfaces;

use App\BLL\Models\AanwezigheidRegel;

interface AanwezigheidRepositoryInterface
{
    /**
     * Alle leden van de aan deze les gekoppelde groep(en), met hun
     * eventueel al geregistreerde aanwezigheidsstatus.
     *
     * @return AanwezigheidRegel[]
     */
    public function vanLes(int $lesId): array;

    /**
     * Slaat de aanwezigheid voor een hele les in één keer op (upsert per lid).
     *
     * @param array<int, array{status: string, opmerking: ?string}> $regels keyed op lid_id
     */
    public function opslaan(int $lesId, array $regels, int $authId): void;
}
