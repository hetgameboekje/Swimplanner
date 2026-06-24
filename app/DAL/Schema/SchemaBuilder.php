<?php

declare(strict_types=1);

namespace App\DAL\Schema;

/**
 * Zet een door SchemaParser uitgelezen tabel-definitie om naar een
 * CREATE TABLE-statement en voert die uit.
 *
 * Let op (bewuste beperking v1): dit doet alleen CREATE TABLE IF NOT EXISTS.
 * Als je een kolom toevoegt aan een XML-bestand van een tabel die al
 * bestaat, gebeurt er niets totdat je de tabel handmatig dropt of
 * --fresh gebruikt. Een ALTER-diff komt pas als daar behoefte aan is.
 */
final class SchemaBuilder
{
    public function __construct(
        private readonly \PDO $connectie,
        private readonly string $collation,
    ) {
    }

    public function bouwTabel(array $tabel): void
    {
        $sql = $this->naarCreateTable($tabel);
        $this->connectie->exec($sql);
    }

    public function dropTabel(string $naam): void
    {
        $this->connectie->exec("DROP TABLE IF EXISTS `{$naam}`");
    }

    private function naarCreateTable(array $tabel): string
    {
        $regels = [];
        $primaryKeyKolommen = [];

        foreach ($tabel['kolommen'] as $kolom) {
            $regels[] = '  ' . $this->kolomDefinitie($kolom);
            if ($kolom['primaryKey']) {
                $primaryKeyKolommen[] = $kolom['naam'];
            }
        }

        if ($primaryKeyKolommen !== []) {
            $regels[] = '  PRIMARY KEY (' . $this->kolomLijst($primaryKeyKolommen) . ')';
        }

        foreach ($tabel['uniekeSets'] as $set) {
            $naam = 'uniq_' . $tabel['naam'] . '_' . implode('_', $set);
            $regels[] = "  UNIQUE KEY `{$naam}` (" . $this->kolomLijst($set) . ')';
        }

        foreach ($tabel['kolommen'] as $kolom) {
            if ($kolom['unique']) {
                $regels[] = "  UNIQUE KEY `uniq_{$tabel['naam']}_{$kolom['naam']}` (`{$kolom['naam']}`)";
            }
            if ($kolom['references'] !== null) {
                [$refTabel, $refKolom] = explode('.', $kolom['references']);
                $regels[] = sprintf(
                    '  FOREIGN KEY (`%s`) REFERENCES `%s`(`%s`) ON DELETE %s ON UPDATE %s',
                    $kolom['naam'],
                    $refTabel,
                    $refKolom,
                    $kolom['onDelete'],
                    $kolom['onUpdate'],
                );
            }
        }

        return sprintf(
            "CREATE TABLE IF NOT EXISTS `%s` (\n%s\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=%s;",
            $tabel['naam'],
            implode(",\n", $regels),
            $this->collation,
        );
    }

    private function kolomDefinitie(array $kolom): string
    {
        $type = strtoupper($kolom['type']);

        if ($type === 'ENUM' && $kolom['values'] !== null) {
            $waarden = array_map(
                static fn (string $w) => "'" . str_replace("'", "\\'", trim($w)) . "'",
                explode(',', $kolom['values']),
            );
            $type .= '(' . implode(',', $waarden) . ')';
        } elseif ($kolom['length'] !== null) {
            $type .= '(' . $kolom['length'] . ')';
        }

        $definitie = "`{$kolom['naam']}` {$type}";
        $definitie .= $kolom['nullable'] ? ' NULL' : ' NOT NULL';

        if ($kolom['default'] !== null) {
            $definitie .= ' DEFAULT ' . $this->defaultWaarde($kolom['default']);
        }

        if ($kolom['onUpdateCurrentTimestamp']) {
            $definitie .= ' ON UPDATE CURRENT_TIMESTAMP';
        }

        if ($kolom['autoIncrement']) {
            $definitie .= ' AUTO_INCREMENT';
        }

        return $definitie;
    }

    private function defaultWaarde(string $waarde): string
    {
        if ($waarde === 'CURRENT_TIMESTAMP' || $waarde === 'NULL' || is_numeric($waarde)) {
            return $waarde;
        }
        return "'" . str_replace("'", "\\'", $waarde) . "'";
    }

    private function kolomLijst(array $kolommen): string
    {
        return implode(',', array_map(static fn (string $k) => "`{$k}`", $kolommen));
    }
}
