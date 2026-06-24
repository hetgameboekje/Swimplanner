<?php

declare(strict_types=1);

namespace App\DAL\Pdo;

use App\BLL\Interfaces\GebruikerRepositoryInterface;
use App\BLL\Models\Gebruiker;
use App\BLL\Models\Rol;
use App\DAL\Database;

final class PdoGebruikerRepository implements GebruikerRepositoryInterface
{
    public function alleInstructeurs(): array
    {
        $rijen = Database::connectie()
            ->query("SELECT * FROM gebruikers WHERE actief = 1 AND rol IN ('instructeur','beheerder') ORDER BY naam")
            ->fetchAll();

        return array_map($this->rijNaarGebruiker(...), $rijen);
    }

    public function zoekOpId(int $id): ?Gebruiker
    {
        $statement = Database::connectie()->prepare('SELECT * FROM gebruikers WHERE id = :id');
        $statement->execute(['id' => $id]);
        $rij = $statement->fetch();

        return $rij === false ? null : $this->rijNaarGebruiker($rij);
    }

    public function loginGegevensVoorEmail(string $email): ?array
    {
        $statement = Database::connectie()->prepare(
            'SELECT id, wachtwoord_hash, actief FROM gebruikers WHERE email = :email'
        );
        $statement->execute(['email' => $email]);
        $rij = $statement->fetch();

        if ($rij === false) {
            return null;
        }

        return [
            'id' => (int) $rij['id'],
            'wachtwoord_hash' => $rij['wachtwoord_hash'],
            'actief' => (bool) $rij['actief'],
        ];
    }

    private function rijNaarGebruiker(array $rij): Gebruiker
    {
        return new Gebruiker((int) $rij['id'], $rij['naam'], $rij['email'], Rol::from($rij['rol']));
    }
}
