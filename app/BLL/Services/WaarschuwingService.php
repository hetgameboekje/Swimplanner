<?php

declare(strict_types=1);

namespace App\BLL\Services;

use App\BLL\Interfaces\LesRepositoryInterface;

/**
 * Genereert informatieve waarschuwingen voor het dashboard.
 * Blokkeert nooit een actie — geeft alleen signalen aan de instructeur.
 */
final class WaarschuwingService
{
    public function __construct(
        private readonly LesRepositoryInterface $lesRepository,
    ) {
    }

    /**
     * @param int[]|null $beperkTotGroepIds Alleen waarschuwingen tonen voor
     *     lessen die minstens één van deze groepen bevatten. null = geen
     *     beperking (voor beheerders, die alles zien).
     * @return string[]
     */
    public function actueleWaarschuwingen(?array $beperkTotGroepIds = null): array
    {
        $waarschuwingen = [];

        foreach ($this->lesRepository->zonderLesplanning() as $les) {
            if ($beperkTotGroepIds !== null) {
                $lesGroepIds = array_map(static fn ($groep) => $groep->id, $les->groepen);
                if (empty(array_intersect($lesGroepIds, $beperkTotGroepIds))) {
                    continue;
                }
            }

            $groepNamen = implode(', ', array_map(static fn ($groep) => $groep->naam, $les->groepen));
            $waarschuwingen[] = sprintf(
                'Les op %s voor groep%s "%s" heeft nog geen lesplanning.',
                $les->datum->format('d-m-Y'),
                count($les->groepen) > 1 ? 'en' : '',
                $groepNamen,
            );
        }

        return $waarschuwingen;
    }
}
