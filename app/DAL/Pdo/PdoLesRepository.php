<?php

declare(strict_types=1);

namespace App\DAL\Pdo;

use App\BLL\Interfaces\GebruikerRepositoryInterface;
use App\BLL\Interfaces\GroepRepositoryInterface;
use App\BLL\Interfaces\LesRepositoryInterface;
use App\BLL\Models\Les;
use App\BLL\Models\LesType;
use App\Core\AuditLogger;
use App\DAL\Database;

/**
 * Bouwt Les-objecten op door Groep en Gebruiker op te vragen via hun eigen
 * repository-interfaces (compositie i.p.v. dezelfde joins dupliceren).
 * Bewust geen query-optimalisatie (N+1) — irrelevant op deze schaal, en
 * voorkomt dat les/groep/gebruiker-mapping op meerdere plekken staat.
 */
final class PdoLesRepository implements LesRepositoryInterface
{
    private const ENTITEIT = 'les';

    public function __construct(
        private readonly GroepRepositoryInterface $groepRepository,
        private readonly GebruikerRepositoryInterface $gebruikerRepository,
    ) {
    }

    public function alle(): array
    {
        $rijen = Database::connectie()
            ->query('SELECT * FROM lessen ORDER BY datum DESC, begin_tijd')
            ->fetchAll();

        return array_map($this->rijNaarLes(...), $rijen);
    }

    public function zoekOpId(int $id): ?Les
    {
        $statement = Database::connectie()->prepare('SELECT * FROM lessen WHERE id = :id');
        $statement->execute(['id' => $id]);
        $rij = $statement->fetch();

        return $rij === false ? null : $this->rijNaarLes($rij);
    }

    public function zonderLesplanning(): array
    {
        $rijen = Database::connectie()
            ->query(
                'SELECT l.* FROM lessen l
                 WHERE NOT EXISTS (SELECT 1 FROM lesplanningen lp WHERE lp.les_id = l.id)
                 ORDER BY l.datum'
            )
            ->fetchAll();

        return array_map($this->rijNaarLes(...), $rijen);
    }

    public function aanmaken(
        array $groepIds,
        \DateTimeImmutable $datum,
        string $type,
        array $instructeurIds,
        ?string $beginTijd,
        ?string $eindTijd,
        int $authId,
    ): int {
        $connectie = Database::connectie();
        $connectie->beginTransaction();

        try {
            $statement = $connectie->prepare(
                'INSERT INTO lessen (datum, begin_tijd, eind_tijd, type, created_by)
                 VALUES (:datum, :begin_tijd, :eind_tijd, :type, :created_by)'
            );
            $statement->execute([
                'datum' => $datum->format('Y-m-d'),
                'begin_tijd' => $beginTijd,
                'eind_tijd' => $eindTijd,
                'type' => $type,
                'created_by' => $authId,
            ]);

            $id = (int) $connectie->lastInsertId();

            $this->koppelGroepen($id, $groepIds);
            $this->koppelInstructeurs($id, $instructeurIds);

            $connectie->commit();
        } catch (\Throwable $fout) {
            $connectie->rollBack();
            throw $fout;
        }

        AuditLogger::log($authId, 'create', self::ENTITEIT, $id, "Les aangemaakt op {$datum->format('d-m-Y')}.");

        return $id;
    }

    public function bijwerken(
        int $id,
        array $groepIds,
        \DateTimeImmutable $datum,
        string $type,
        array $instructeurIds,
        ?string $beginTijd,
        ?string $eindTijd,
        int $authId,
    ): void {
        $connectie = Database::connectie();
        $connectie->beginTransaction();

        try {
            $statement = $connectie->prepare(
                'UPDATE lessen SET datum = :datum, begin_tijd = :begin_tijd, eind_tijd = :eind_tijd, type = :type
                 WHERE id = :id'
            );
            $statement->execute([
                'datum' => $datum->format('Y-m-d'),
                'begin_tijd' => $beginTijd,
                'eind_tijd' => $eindTijd,
                'type' => $type,
                'id' => $id,
            ]);

            $this->koppelGroepen($id, $groepIds, opnieuw: true);
            $this->koppelInstructeurs($id, $instructeurIds, opnieuw: true);

            $connectie->commit();
        } catch (\Throwable $fout) {
            $connectie->rollBack();
            throw $fout;
        }

        AuditLogger::log($authId, 'update', self::ENTITEIT, $id, "Les bijgewerkt naar datum {$datum->format('d-m-Y')}.");
    }

    public function verwijderen(int $id, int $authId): void
    {
        $statement = Database::connectie()->prepare('DELETE FROM lessen WHERE id = :id');
        $statement->execute(['id' => $id]);

        AuditLogger::log($authId, 'delete', self::ENTITEIT, $id, 'Les verwijderd.');
    }

    /** @param int[] $groepIds */
    private function koppelGroepen(int $lesId, array $groepIds, bool $opnieuw = false): void
    {
        $connectie = Database::connectie();

        if ($opnieuw) {
            $connectie->prepare('DELETE FROM les_groepen WHERE les_id = :les_id')->execute(['les_id' => $lesId]);
        }

        $invoegen = $connectie->prepare('INSERT INTO les_groepen (les_id, groep_id) VALUES (:les_id, :groep_id)');
        foreach (array_unique($groepIds) as $groepId) {
            $invoegen->execute(['les_id' => $lesId, 'groep_id' => $groepId]);
        }
    }

    /** @param int[] $instructeurIds */
    private function koppelInstructeurs(int $lesId, array $instructeurIds, bool $opnieuw = false): void
    {
        $connectie = Database::connectie();

        if ($opnieuw) {
            $connectie->prepare('DELETE FROM les_instructeurs WHERE les_id = :les_id')->execute(['les_id' => $lesId]);
        }

        $invoegen = $connectie->prepare(
            'INSERT INTO les_instructeurs (les_id, gebruiker_id) VALUES (:les_id, :gebruiker_id)'
        );
        foreach (array_unique($instructeurIds) as $gebruikerId) {
            $invoegen->execute(['les_id' => $lesId, 'gebruiker_id' => $gebruikerId]);
        }
    }

    private function rijNaarLes(array $rij): Les
    {
        $lesId = (int) $rij['id'];

        $groepen = array_map(
            fn (int $groepId) => $this->groepRepository->zoekOpId($groepId),
            $this->idsUitKoppeltabel('les_groepen', 'groep_id', $lesId),
        );
        $instructeurs = array_map(
            fn (int $gebruikerId) => $this->gebruikerRepository->zoekOpId($gebruikerId),
            $this->idsUitKoppeltabel('les_instructeurs', 'gebruiker_id', $lesId),
        );

        if (in_array(null, $groepen, true) || in_array(null, $instructeurs, true)) {
            throw new \RuntimeException("Inconsistente data voor les {$lesId}: groep of instructeur ontbreekt.");
        }

        $bestaatStatement = Database::connectie()->prepare(
            'SELECT EXISTS(SELECT 1 FROM lesplanningen WHERE les_id = :les_id)'
        );
        $bestaatStatement->execute(['les_id' => $lesId]);
        $heeftLesplanning = (bool) $bestaatStatement->fetchColumn();

        return new Les(
            $lesId,
            $groepen,
            new \DateTimeImmutable($rij['datum']),
            LesType::from($rij['type']),
            $instructeurs,
            $rij['begin_tijd'],
            $rij['eind_tijd'],
            $heeftLesplanning,
        );
    }

    /** @return int[] */
    private function idsUitKoppeltabel(string $tabel, string $kolom, int $lesId): array
    {
        $statement = Database::connectie()->prepare(
            "SELECT {$kolom} FROM {$tabel} WHERE les_id = :les_id"
        );
        $statement->execute(['les_id' => $lesId]);

        return array_map('intval', $statement->fetchAll(\PDO::FETCH_COLUMN));
    }
}
