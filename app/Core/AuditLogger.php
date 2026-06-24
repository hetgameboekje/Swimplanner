<?php

declare(strict_types=1);

namespace App\Core;

use App\DAL\Database;

/**
 * Centrale audit-logger. Wordt aangeroepen vanuit de Pdo-repositories ná
 * elke create/update/delete — nooit verspreid vanuit Controllers, zodat
 * logging niet per ongeluk vergeten kan worden bij een nieuwe actie.
 */
final class AuditLogger
{
    public static function log(int $authId, string $actie, string $entiteit, ?int $recordId, ?string $samenvatting = null): void
    {
        $statement = Database::connectie()->prepare(
            'INSERT INTO audit_logs (gebruiker_id, actie, entiteit, record_id, samenvatting, ip_adres)
             VALUES (:gebruiker_id, :actie, :entiteit, :record_id, :samenvatting, :ip_adres)'
        );

        $statement->execute([
            'gebruiker_id' => $authId,
            'actie' => $actie,
            'entiteit' => $entiteit,
            'record_id' => $recordId,
            'samenvatting' => $samenvatting,
            'ip_adres' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    }
}
