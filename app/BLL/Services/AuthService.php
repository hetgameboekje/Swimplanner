<?php

declare(strict_types=1);

namespace App\BLL\Services;

use App\BLL\Interfaces\GebruikerRepositoryInterface;
use App\BLL\Models\Gebruiker;

final class AuthService
{
    public function __construct(
        private readonly GebruikerRepositoryInterface $gebruikerRepository,
    ) {
    }

    public function inloggen(string $email, string $wachtwoord): ?Gebruiker
    {
        $email = trim($email);
        if ($email === '' || $wachtwoord === '') {
            return null;
        }

        $loginGegevens = $this->gebruikerRepository->loginGegevensVoorEmail($email);
        if ($loginGegevens === null || !$loginGegevens['actief']) {
            return null;
        }

        if (!password_verify($wachtwoord, $loginGegevens['wachtwoord_hash'])) {
            return null;
        }

        return $this->gebruikerRepository->zoekOpId($loginGegevens['id']);
    }
}
