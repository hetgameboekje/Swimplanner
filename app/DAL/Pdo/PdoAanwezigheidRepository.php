<?php

declare(strict_types=1);

namespace App\DAL\Pdo;

use App\BLL\Interfaces\AanwezigheidRepositoryInterface;
use App\BLL\Models\AanwezigheidRegel;
use App\BLL\Models\AanwezigheidStatus;
use App\BLL\Models\Lid;
use App\Core\AuditLogger;
use App\DAL\Database;

final class PdoAanwezigheidRepository implements AanwezigheidRepositoryInterface
{
    private const ENTITEIT = 'aanwezigheid';

    public function vanLes(int $lesId): array
    {
        $statement = Database::connectie()->prepare(
            'SELECT DISTINCT l.*, a.status AS aanwezigheid_status, a.opmerking AS aanwezigheid_opmerking
             FROM leden l
             JOIN groep_leden gl ON gl.lid_id = l.id AND gl.datum_uitgeschreven IS NULL
             JOIN les_groepen lg ON lg.groep_id = gl.groep_id
             LEFT JOIN aanwezigheid a ON a.les_id = lg.les_id AND a.lid_id = l.id
             WHERE lg.les_id = :les_id
             ORDER BY l.achternaam, l.voornaam'
        );
        $statement->execute(['les_id' => $lesId]);

        return array_map($this->rijNaarRegel(...), $statement->fetchAll());
    }

    public function opslaan(int $lesId, array $regels, int $authId): void
    {
        $connectie = Database::connectie();
        $connectie->beginTransaction();

        try {
            $statement = $connectie->prepare(
                'INSERT INTO aanwezigheid (les_id, lid_id, status, opmerking, geregistreerd_door)
                 VALUES (:les_id, :lid_id, :status, :opmerking, :geregistreerd_door)
                 ON DUPLICATE KEY UPDATE
                    status = VALUES(status),
                    opmerking = VALUES(opmerking),
                    geregistreerd_door = VALUES(geregistreerd_door),
                    geregistreerd_op = CURRENT_TIMESTAMP'
            );

            foreach ($regels as $lidId => $regel) {
                $statement->execute([
                    'les_id' => $lesId,
                    'lid_id' => $lidId,
                    'status' => $regel['status'],
                    'opmerking' => $regel['opmerking'],
                    'geregistreerd_door' => $authId,
                ]);
            }

            $connectie->commit();
        } catch (\Throwable $fout) {
            $connectie->rollBack();
            throw $fout;
        }

        $telPerStatus = array_count_values(array_column($regels, 'status'));
        $samenvatting = implode(', ', array_map(
            static fn (string $status, int $aantal) => "{$status}: {$aantal}",
            array_keys($telPerStatus),
            $telPerStatus,
        ));

        AuditLogger::log($authId, 'update', self::ENTITEIT, $lesId, "Aanwezigheid geregistreerd voor les {$lesId} ({$samenvatting}).");
    }

    private function rijNaarRegel(array $rij): AanwezigheidRegel
    {
        $lid = new Lid(
            (int) $rij['id'],
            $rij['voornaam'],
            $rij['achternaam'],
            $rij['geboortejaar'] !== null ? (int) $rij['geboortejaar'] : null,
            $rij['contactgegevens'],
            (bool) $rij['actief'],
        );

        return new AanwezigheidRegel(
            $lid,
            $rij['aanwezigheid_status'] !== null ? AanwezigheidStatus::from($rij['aanwezigheid_status']) : null,
            $rij['aanwezigheid_opmerking'],
        );
    }
}
