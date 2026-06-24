<?php

declare(strict_types=1);

namespace App\DAL\Pdo;

use App\BLL\Interfaces\LidRepositoryInterface;
use App\BLL\Models\Lid;
use App\Core\AuditLogger;
use App\DAL\Database;

final class PdoLidRepository implements LidRepositoryInterface
{
    private const ENTITEIT = 'lid';

    public function ledenVanGroep(int $groepId): array
    {
        $statement = Database::connectie()->prepare(
            'SELECT l.* FROM leden l
             JOIN groep_leden gl ON gl.lid_id = l.id
             WHERE gl.groep_id = :groep_id AND gl.datum_uitgeschreven IS NULL
             ORDER BY l.achternaam, l.voornaam'
        );
        $statement->execute(['groep_id' => $groepId]);

        return array_map($this->rijNaarLid(...), $statement->fetchAll());
    }

    public function zoekOpId(int $id): ?Lid
    {
        $statement = Database::connectie()->prepare('SELECT * FROM leden WHERE id = :id');
        $statement->execute(['id' => $id]);
        $rij = $statement->fetch();

        return $rij === false ? null : $this->rijNaarLid($rij);
    }

    public function aanmakenEnKoppelen(
        string $voornaam,
        string $achternaam,
        ?int $geboortejaar,
        ?string $contactgegevens,
        int $groepId,
        int $authId,
    ): int {
        $connectie = Database::connectie();
        $connectie->beginTransaction();

        try {
            $statement = $connectie->prepare(
                'INSERT INTO leden (voornaam, achternaam, geboortejaar, contactgegevens)
                 VALUES (:voornaam, :achternaam, :geboortejaar, :contactgegevens)'
            );
            $statement->execute([
                'voornaam' => $voornaam,
                'achternaam' => $achternaam,
                'geboortejaar' => $geboortejaar,
                'contactgegevens' => $contactgegevens,
            ]);

            $lidId = (int) $connectie->lastInsertId();

            $koppel = $connectie->prepare(
                'INSERT INTO groep_leden (groep_id, lid_id, datum_aangemeld) VALUES (:groep_id, :lid_id, :datum_aangemeld)'
            );
            $koppel->execute([
                'groep_id' => $groepId,
                'lid_id' => $lidId,
                'datum_aangemeld' => (new \DateTimeImmutable())->format('Y-m-d'),
            ]);

            $connectie->commit();
        } catch (\Throwable $fout) {
            $connectie->rollBack();
            throw $fout;
        }

        AuditLogger::log($authId, 'create', self::ENTITEIT, $lidId, "Lid '{$voornaam} {$achternaam}' aangemaakt en gekoppeld aan groep {$groepId}.");

        return $lidId;
    }

    public function uitschrijven(int $groepId, int $lidId, int $authId): void
    {
        $statement = Database::connectie()->prepare(
            'UPDATE groep_leden SET datum_uitgeschreven = :datum
             WHERE groep_id = :groep_id AND lid_id = :lid_id AND datum_uitgeschreven IS NULL'
        );
        $statement->execute([
            'datum' => (new \DateTimeImmutable())->format('Y-m-d'),
            'groep_id' => $groepId,
            'lid_id' => $lidId,
        ]);

        AuditLogger::log($authId, 'update', self::ENTITEIT, $lidId, "Lid uitgeschreven bij groep {$groepId}.");
    }

    private function rijNaarLid(array $rij): Lid
    {
        return new Lid(
            (int) $rij['id'],
            $rij['voornaam'],
            $rij['achternaam'],
            $rij['geboortejaar'] !== null ? (int) $rij['geboortejaar'] : null,
            $rij['contactgegevens'],
            (bool) $rij['actief'],
        );
    }
}
