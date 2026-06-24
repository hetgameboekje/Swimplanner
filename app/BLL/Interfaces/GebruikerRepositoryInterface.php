<?php

declare(strict_types=1);

namespace App\BLL\Interfaces;

use App\BLL\Models\Gebruiker;

interface GebruikerRepositoryInterface
{
    /** @return Gebruiker[] */
    public function alleInstructeurs(): array;

    public function zoekOpId(int $id): ?Gebruiker;

    /**
     * Levert alleen de gegevens die nodig zijn om een wachtwoord te
     * verifiëren — bewust geen Gebruiker-model, zodat de wachtwoord-hash
     * nooit in het domeinmodel terechtkomt.
     *
     * @return array{id: int, wachtwoord_hash: string, actief: bool}|null
     */
    public function loginGegevensVoorEmail(string $email): ?array;
}
