<?php

declare(strict_types=1);

namespace App\BLL\Services;

use App\BLL\Interfaces\GebruikerRepositoryInterface;
use App\BLL\Models\Gebruiker;

final class GebruikerService
{
    public function __construct(
        private readonly GebruikerRepositoryInterface $gebruikerRepository,
    ) {
    }

    /** @return Gebruiker[] */
    public function alleInstructeurs(): array
    {
        return $this->gebruikerRepository->alleInstructeurs();
    }

    public function zoekOpId(int $id): ?Gebruiker
    {
        return $this->gebruikerRepository->zoekOpId($id);
    }
}
