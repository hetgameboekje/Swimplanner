<?php

declare(strict_types=1);

namespace App\DAL\Pdo;

use App\BLL\Interfaces\GebruikerRepositoryInterface;
use App\BLL\Interfaces\GroepRepositoryInterface;
use App\BLL\Interfaces\LesplanningRepositoryInterface;
use App\BLL\Interfaces\MateriaalRepositoryInterface;
use App\BLL\Models\Lesplanning;
use App\BLL\Models\LesplanningOnderdeel;
use App\Core\AuditLogger;
use App\DAL\Database;

/**
 * Net als PdoLesRepository: bouwt Lesplanning-objecten op via de eigen
 * Groep/Gebruiker/Materiaal-repositories (compositie), in plaats van dezelfde
 * joins te dupliceren.
 */
final class PdoLesplanningRepository implements LesplanningRepositoryInterface
{
    private const ENTITEIT = 'lesplanning';

    public function __construct(
        private readonly GroepRepositoryInterface $groepRepository,
        private readonly GebruikerRepositoryInterface $gebruikerRepository,
        private readonly MateriaalRepositoryInterface $materiaalRepository,
    ) {
    }

    public function alle(): array
    {
        $rijen = Database::connectie()->query('SELECT * FROM lesplanningen ORDER BY datum DESC, begin_tijd')->fetchAll();

        return array_map($this->rijNaarLesplanning(...), $rijen);
    }

    public function zoekOpId(int $id): ?Lesplanning
    {
        $statement = Database::connectie()->prepare('SELECT * FROM lesplanningen WHERE id = :id');
        $statement->execute(['id' => $id]);
        $rij = $statement->fetch();

        return $rij === false ? null : $this->rijNaarLesplanning($rij);
    }

    public function aanmaken(
        int $groepId,
        int $instructeurId,
        ?int $lesId,
        \DateTimeImmutable $datum,
        string $beginTijd,
        string $eindTijd,
        ?string $locatie,
        ?string $beginsituatie,
        ?string $doelstelling,
        array $onderdelen,
        int $authId,
    ): int {
        $connectie = Database::connectie();
        $connectie->beginTransaction();

        try {
            $statement = $connectie->prepare(
                'INSERT INTO lesplanningen (les_id, groep_id, instructeur_id, datum, begin_tijd, eind_tijd, locatie, beginsituatie, doelstelling)
                 VALUES (:les_id, :groep_id, :instructeur_id, :datum, :begin_tijd, :eind_tijd, :locatie, :beginsituatie, :doelstelling)'
            );
            $statement->execute([
                'les_id' => $lesId,
                'groep_id' => $groepId,
                'instructeur_id' => $instructeurId,
                'datum' => $datum->format('Y-m-d'),
                'begin_tijd' => $beginTijd,
                'eind_tijd' => $eindTijd,
                'locatie' => $locatie,
                'beginsituatie' => $beginsituatie,
                'doelstelling' => $doelstelling,
            ]);

            $id = (int) $connectie->lastInsertId();

            $this->onderdelenOpslaan($id, $onderdelen);

            $connectie->commit();
        } catch (\Throwable $fout) {
            $connectie->rollBack();
            throw $fout;
        }

        AuditLogger::log($authId, 'create', self::ENTITEIT, $id, "Lesplanning aangemaakt voor {$datum->format('d-m-Y')}.");

        return $id;
    }

    public function bijwerken(
        int $id,
        int $groepId,
        int $instructeurId,
        ?int $lesId,
        \DateTimeImmutable $datum,
        string $beginTijd,
        string $eindTijd,
        ?string $locatie,
        ?string $beginsituatie,
        ?string $doelstelling,
        array $onderdelen,
        int $authId,
    ): void {
        $connectie = Database::connectie();
        $connectie->beginTransaction();

        try {
            $statement = $connectie->prepare(
                'UPDATE lesplanningen SET les_id = :les_id, groep_id = :groep_id, instructeur_id = :instructeur_id,
                    datum = :datum, begin_tijd = :begin_tijd, eind_tijd = :eind_tijd, locatie = :locatie,
                    beginsituatie = :beginsituatie, doelstelling = :doelstelling
                 WHERE id = :id'
            );
            $statement->execute([
                'les_id' => $lesId,
                'groep_id' => $groepId,
                'instructeur_id' => $instructeurId,
                'datum' => $datum->format('Y-m-d'),
                'begin_tijd' => $beginTijd,
                'eind_tijd' => $eindTijd,
                'locatie' => $locatie,
                'beginsituatie' => $beginsituatie,
                'doelstelling' => $doelstelling,
                'id' => $id,
            ]);

            $connectie->prepare('DELETE FROM lesplanning_onderdelen WHERE lesplanning_id = :id')->execute(['id' => $id]);
            $this->onderdelenOpslaan($id, $onderdelen);

            $connectie->commit();
        } catch (\Throwable $fout) {
            $connectie->rollBack();
            throw $fout;
        }

        AuditLogger::log($authId, 'update', self::ENTITEIT, $id, "Lesplanning bijgewerkt naar datum {$datum->format('d-m-Y')}.");
    }

    public function verwijderen(int $id, int $authId): void
    {
        $statement = Database::connectie()->prepare('DELETE FROM lesplanningen WHERE id = :id');
        $statement->execute(['id' => $id]);

        AuditLogger::log($authId, 'delete', self::ENTITEIT, $id, 'Lesplanning verwijderd.');
    }

    /**
     * @param array<int, array{naam: string, tijdIndicatie: ?string, doel: ?string, activiteit: ?string, organisatieEnMaterialen: ?string, didactischeAanwijzingen: ?string, materiaalIds: int[]}> $onderdelen
     */
    private function onderdelenOpslaan(int $lesplanningId, array $onderdelen): void
    {
        $connectie = Database::connectie();

        $onderdeelStatement = $connectie->prepare(
            'INSERT INTO lesplanning_onderdelen (lesplanning_id, volgnummer, naam, tijd_indicatie, doel, activiteit, organisatie_en_materialen, didactische_aanwijzingen)
             VALUES (:lesplanning_id, :volgnummer, :naam, :tijd_indicatie, :doel, :activiteit, :organisatie_en_materialen, :didactische_aanwijzingen)'
        );
        $materiaalStatement = $connectie->prepare(
            'INSERT INTO onderdeel_materialen (lesplanning_onderdeel_id, materiaal_id) VALUES (:onderdeel_id, :materiaal_id)'
        );

        $volgnummer = 1;
        foreach ($onderdelen as $onderdeel) {
            $onderdeelStatement->execute([
                'lesplanning_id' => $lesplanningId,
                'volgnummer' => $volgnummer,
                'naam' => $onderdeel['naam'],
                'tijd_indicatie' => $onderdeel['tijdIndicatie'],
                'doel' => $onderdeel['doel'],
                'activiteit' => $onderdeel['activiteit'],
                'organisatie_en_materialen' => $onderdeel['organisatieEnMaterialen'],
                'didactische_aanwijzingen' => $onderdeel['didactischeAanwijzingen'],
            ]);
            $onderdeelId = (int) $connectie->lastInsertId();

            foreach (array_unique($onderdeel['materiaalIds']) as $materiaalId) {
                $materiaalStatement->execute(['onderdeel_id' => $onderdeelId, 'materiaal_id' => $materiaalId]);
            }

            $volgnummer++;
        }
    }

    private function rijNaarLesplanning(array $rij): Lesplanning
    {
        $groep = $this->groepRepository->zoekOpId((int) $rij['groep_id']);
        $instructeur = $this->gebruikerRepository->zoekOpId((int) $rij['instructeur_id']);

        if ($groep === null || $instructeur === null) {
            throw new \RuntimeException("Inconsistente data voor lesplanning {$rij['id']}: groep of instructeur ontbreekt.");
        }

        return new Lesplanning(
            (int) $rij['id'],
            $groep,
            $instructeur,
            new \DateTimeImmutable($rij['datum']),
            substr($rij['begin_tijd'], 0, 5),
            substr($rij['eind_tijd'], 0, 5),
            $rij['locatie'] ?? '',
            $rij['beginsituatie'] ?? '',
            $rij['doelstelling'] ?? '',
            $this->onderdelenVanLesplanning((int) $rij['id']),
            $rij['les_id'] !== null ? (int) $rij['les_id'] : null,
        );
    }

    /** @return LesplanningOnderdeel[] */
    private function onderdelenVanLesplanning(int $lesplanningId): array
    {
        $statement = Database::connectie()->prepare(
            'SELECT * FROM lesplanning_onderdelen WHERE lesplanning_id = :id ORDER BY volgnummer'
        );
        $statement->execute(['id' => $lesplanningId]);

        return array_map(
            fn (array $rij) => new LesplanningOnderdeel(
                (int) $rij['volgnummer'],
                $rij['naam'],
                $rij['tijd_indicatie'] ?? '',
                $rij['doel'] ?? '',
                $rij['activiteit'] ?? '',
                $rij['organisatie_en_materialen'] ?? '',
                $rij['didactische_aanwijzingen'] ?? '',
                $this->materialenVanOnderdeel((int) $rij['id']),
            ),
            $statement->fetchAll(),
        );
    }

    /** @return \App\BLL\Models\Materiaal[] */
    private function materialenVanOnderdeel(int $onderdeelId): array
    {
        $statement = Database::connectie()->prepare(
            'SELECT materiaal_id FROM onderdeel_materialen WHERE lesplanning_onderdeel_id = :id'
        );
        $statement->execute(['id' => $onderdeelId]);

        $materiaalIds = array_map('intval', $statement->fetchAll(\PDO::FETCH_COLUMN));

        return array_values(array_filter(array_map(
            fn (int $materiaalId) => $this->materiaalRepository->zoekOpId($materiaalId),
            $materiaalIds,
        )));
    }
}
