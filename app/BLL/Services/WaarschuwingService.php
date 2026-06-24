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

    /** @return string[] */
    public function actueleWaarschuwingen(): array
    {
        $waarschuwingen = [];

        foreach ($this->lesRepository->zonderLesplanning() as $les) {
            $waarschuwingen[] = sprintf(
                'Les op %s voor groep "%s" heeft nog geen lesplanning.',
                $les->datum->format('d-m-Y'),
                $les->groep->naam,
            );
        }

        return $waarschuwingen;
    }
}
