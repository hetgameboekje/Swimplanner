<?php

declare(strict_types=1);

namespace App\DAL\Pdo;

use App\BLL\Interfaces\MateriaalRepositoryInterface;
use App\BLL\Models\Materiaal;
use App\Core\AuditLogger;
use App\DAL\Database;

final class PdoMateriaalRepository implements MateriaalRepositoryInterface
{
    private const ENTITEIT = 'materiaal';

    public function alle(): array
    {
        $rijen = Database::connectie()->query('SELECT * FROM materialen ORDER BY naam')->fetchAll();

        return array_map($this->rijNaarMateriaal(...), $rijen);
    }

    public function zoekOpId(int $id): ?Materiaal
    {
        $statement = Database::connectie()->prepare('SELECT * FROM materialen WHERE id = :id');
        $statement->execute(['id' => $id]);
        $rij = $statement->fetch();

        return $rij === false ? null : $this->rijNaarMateriaal($rij);
    }

    public function aanmaken(string $naam, ?string $categorie, int $authId): int
    {
        $statement = Database::connectie()->prepare(
            'INSERT INTO materialen (naam, categorie, created_by) VALUES (:naam, :categorie, :created_by)'
        );
        $statement->execute([
            'naam' => $naam,
            'categorie' => $categorie,
            'created_by' => $authId,
        ]);

        $id = (int) Database::connectie()->lastInsertId();

        AuditLogger::log($authId, 'create', self::ENTITEIT, $id, "Materiaal '{$naam}' aangemaakt.");

        return $id;
    }

    public function bijwerken(int $id, string $naam, ?string $categorie, int $authId): void
    {
        $statement = Database::connectie()->prepare(
            'UPDATE materialen SET naam = :naam, categorie = :categorie, updated_by = :updated_by WHERE id = :id'
        );
        $statement->execute([
            'naam' => $naam,
            'categorie' => $categorie,
            'updated_by' => $authId,
            'id' => $id,
        ]);

        AuditLogger::log($authId, 'update', self::ENTITEIT, $id, "Materiaal bijgewerkt naar naam '{$naam}'.");
    }

    public function verwijderen(int $id, int $authId): void
    {
        $statement = Database::connectie()->prepare('DELETE FROM materialen WHERE id = :id');
        $statement->execute(['id' => $id]);

        AuditLogger::log($authId, 'delete', self::ENTITEIT, $id, 'Materiaal verwijderd.');
    }

    public function actiefWijzigen(int $id, bool $actief, int $authId): void
    {
        $statement = Database::connectie()->prepare(
            'UPDATE materialen SET actief = :actief, updated_by = :updated_by WHERE id = :id'
        );
        $statement->execute(['actief' => $actief ? 1 : 0, 'updated_by' => $authId, 'id' => $id]);

        AuditLogger::log($authId, 'update', self::ENTITEIT, $id, $actief ? 'Materiaal geactiveerd.' : 'Materiaal gedeactiveerd.');
    }

    private function rijNaarMateriaal(array $rij): Materiaal
    {
        return new Materiaal((int) $rij['id'], $rij['naam'], $rij['categorie'], (bool) $rij['actief']);
    }
}
