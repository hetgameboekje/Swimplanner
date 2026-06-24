<?php

declare(strict_types=1);

namespace App\BLL\Services;

use App\BLL\Models\AanwezigheidStatus;
use App\BLL\Models\Groep;

/**
 * Bouwt een matrix (leden x lesdatums) per groep, voor een overzicht van
 * wie wel/niet aanwezig is geweest. Hergebruikt de bestaande services in
 * plaats van losse queries — geen optimalisatie nodig op deze schaal.
 */
final class AanwezigheidOverzichtService
{
    public function __construct(
        private readonly LesService $lesService,
        private readonly LidService $lidService,
        private readonly AanwezigheidService $aanwezigheidService,
    ) {
    }

    /**
     * @param Groep[] $groepen
     * @return array<int, array{groep: Groep, lessen: \App\BLL\Models\Les[], leden: \App\BLL\Models\Lid[], matrix: array<int, array<int, ?AanwezigheidStatus>>}>
     */
    public function voorGroepen(array $groepen): array
    {
        $alleLessen = $this->lesService->alleLessen();
        $overzicht = [];

        foreach ($groepen as $groep) {
            $lessenVanGroep = array_values(array_filter(
                $alleLessen,
                static function ($les) use ($groep): bool {
                    foreach ($les->groepen as $lesGroep) {
                        if ($lesGroep->id === $groep->id) {
                            return true;
                        }
                    }
                    return false;
                },
            ));
            usort($lessenVanGroep, static fn ($a, $b) => $a->datum <=> $b->datum);

            $leden = $this->lidService->ledenVanGroep($groep->id);

            $matrix = [];
            foreach ($lessenVanGroep as $les) {
                foreach ($this->aanwezigheidService->voorLes($les->id) as $regel) {
                    $matrix[$regel->lid->id][$les->id] = $regel->status;
                }
            }

            $overzicht[] = [
                'groep' => $groep,
                'lessen' => $lessenVanGroep,
                'leden' => $leden,
                'matrix' => $matrix,
            ];
        }

        return $overzicht;
    }
}
