<?php

declare(strict_types=1);

namespace App\DAL\Pdo;

use App\BLL\Interfaces\GroepRepositoryInterface;
use App\BLL\Models\Afdeling;
use App\BLL\Models\Gebruiker;
use App\BLL\Models\Groep;
use App\BLL\Models\Rol;
use App\Core\AuditLogger;
use App\DAL\Database;
use PDO;

final class PdoGroepRepository implements GroepRepositoryInterface
{
    private const ENTITEIT = 'groep';

    public function alle(): array
    {
        $rijen = Database::connectie()
            ->query(
                'SELECT g.*, a.naam AS afdeling_naam, a.actief AS afdeling_actief
                 FROM groepen g
                 JOIN afdelingen a ON a.id = g.afdeling_id
                 ORDER BY g.naam'
            )
            ->fetchAll();

        return array_map($this->rijNaarGroep(...), $rijen);
    }

    public function zoekOpId(int $id): ?Groep
    {
        $statement = Database::connectie()->prepare(
            'SELECT g.*, a.naam AS afdeling_naam, a.actief AS afdeling_actief
             FROM groepen g
             JOIN afdelingen a ON a.id = g.afdeling_id
             WHERE g.id = :id'
        );
        $statement->execute(['id' => $id]);
        $rij = $statement->fetch();

        return $rij === false ? null : $this->rijNaarGroep($rij);
    }

    public function vanInstructeur(int $instructeurId): array
    {
        $statement = Database::connectie()->prepare(
            'SELECT g.*, a.naam AS afdeling_naam, a.actief AS afdeling_actief
             FROM groepen g
             JOIN afdelingen a ON a.id = g.afdeling_id
             JOIN groep_instructeurs gi ON gi.groep_id = g.id
             WHERE gi.gebruiker_id = :gebruiker_id
             ORDER BY g.naam'
        );
        $statement->execute(['gebruiker_id' => $instructeurId]);

        return array_map($this->rijNaarGroep(...), $statement->fetchAll());
    }

    public function aanmaken(
        string $naam,
        int $afdelingId,
        \DateTimeImmutable $startDatum,
        ?\DateTimeImmutable $eindDatum,
        int $authId,
    ): int {
        $statement = Database::connectie()->prepare(
            'INSERT INTO groepen (afdeling_id, naam, start_datum, eind_datum, created_by)
             VALUES (:afdeling_id, :naam, :start_datum, :eind_datum, :created_by)'
        );
        $statement->execute([
            'afdeling_id' => $afdelingId,
            'naam' => $naam,
            'start_datum' => $startDatum->format('Y-m-d'),
            'eind_datum' => $eindDatum?->format('Y-m-d'),
            'created_by' => $authId,
        ]);

        $id = (int) Database::connectie()->lastInsertId();

        AuditLogger::log($authId, 'create', self::ENTITEIT, $id, "Groep '{$naam}' aangemaakt.");

        return $id;
    }

    public function bijwerken(
        int $id,
        string $naam,
        int $afdelingId,
        \DateTimeImmutable $startDatum,
        ?\DateTimeImmutable $eindDatum,
        int $authId,
    ): void {
        $statement = Database::connectie()->prepare(
            'UPDATE groepen SET naam = :naam, afdeling_id = :afdeling_id, start_datum = :start_datum, eind_datum = :eind_datum
             WHERE id = :id'
        );
        $statement->execute([
            'naam' => $naam,
            'afdeling_id' => $afdelingId,
            'start_datum' => $startDatum->format('Y-m-d'),
            'eind_datum' => $eindDatum?->format('Y-m-d'),
            'id' => $id,
        ]);

        AuditLogger::log($authId, 'update', self::ENTITEIT, $id, "Groep bijgewerkt naar naam '{$naam}'.");
    }

    public function verwijderen(int $id, int $authId): void
    {
        $statement = Database::connectie()->prepare('DELETE FROM groepen WHERE id = :id');
        $statement->execute(['id' => $id]);

        AuditLogger::log($authId, 'delete', self::ENTITEIT, $id, 'Groep verwijderd.');
    }

    public function actiefWijzigen(int $id, bool $actief, int $authId): void
    {
        $statement = Database::connectie()->prepare('UPDATE groepen SET actief = :actief WHERE id = :id');
        $statement->execute(['actief' => $actief ? 1 : 0, 'id' => $id]);

        AuditLogger::log($authId, 'update', self::ENTITEIT, $id, $actief ? 'Groep geactiveerd.' : 'Groep gedeactiveerd.');
    }

    public function instructeursInstellen(int $groepId, array $gebruikerIds, int $authId): void
    {
        $connectie = Database::connectie();
        $connectie->beginTransaction();

        try {
            $verwijder = $connectie->prepare('DELETE FROM groep_instructeurs WHERE groep_id = :groep_id');
            $verwijder->execute(['groep_id' => $groepId]);

            $invoegen = $connectie->prepare(
                'INSERT INTO groep_instructeurs (groep_id, gebruiker_id) VALUES (:groep_id, :gebruiker_id)'
            );
            foreach (array_unique($gebruikerIds) as $gebruikerId) {
                $invoegen->execute(['groep_id' => $groepId, 'gebruiker_id' => $gebruikerId]);
            }

            $connectie->commit();
        } catch (\Throwable $fout) {
            $connectie->rollBack();
            throw $fout;
        }

        AuditLogger::log(
            $authId,
            'update',
            self::ENTITEIT,
            $groepId,
            'Instructeurs bijgewerkt: ' . (empty($gebruikerIds) ? '(geen)' : implode(',', $gebruikerIds)),
        );
    }

    private function rijNaarGroep(array $rij): Groep
    {
        $groepId = (int) $rij['id'];

        return new Groep(
            $groepId,
            $rij['naam'],
            new Afdeling((int) $rij['afdeling_id'], $rij['afdeling_naam'], (bool) $rij['afdeling_actief']),
            new \DateTimeImmutable($rij['start_datum']),
            $rij['eind_datum'] !== null ? new \DateTimeImmutable($rij['eind_datum']) : null,
            $this->instructeursVanGroep($groepId),
            $this->aantalLedenVanGroep($groepId),
            (bool) $rij['actief'],
        );
    }

    /** @return Gebruiker[] */
    private function instructeursVanGroep(int $groepId): array
    {
        $statement = Database::connectie()->prepare(
            'SELECT u.* FROM gebruikers u
             JOIN groep_instructeurs gi ON gi.gebruiker_id = u.id
             WHERE gi.groep_id = :groep_id
             ORDER BY u.naam'
        );
        $statement->execute(['groep_id' => $groepId]);

        return array_map(
            static fn (array $rij) => new Gebruiker((int) $rij['id'], $rij['naam'], $rij['email'], Rol::from($rij['rol'])),
            $statement->fetchAll(),
        );
    }

    private function aantalLedenVanGroep(int $groepId): int
    {
        $statement = Database::connectie()->prepare(
            'SELECT COUNT(*) FROM groep_leden WHERE groep_id = :groep_id AND datum_uitgeschreven IS NULL'
        );
        $statement->execute(['groep_id' => $groepId]);

        return (int) $statement->fetchColumn();
    }
}
