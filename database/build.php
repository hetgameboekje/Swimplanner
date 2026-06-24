<?php

declare(strict_types=1);

/**
 * Bouwt de database + tabellen op basis van de XML-bestanden in
 * database/schema/tables/. Gebruik:
 *
 *   php database/build.php          (maakt database/tabellen aan als ze nog niet bestaan)
 *   php database/build.php --fresh  (DROPt eerst alle tabellen — alleen voor lokale dev!)
 *
 * Voeg een nieuwe tabel toe door een XML-bestand toe te voegen aan
 * database/schema/tables/ (bestandsnaam met nummer-prefix bepaalt de
 * aanmaakvolgorde, belangrijk vanwege foreign keys).
 */

require __DIR__ . '/../vendor/autoload.php';

use App\DAL\Schema\SchemaBuilder;
use App\DAL\Schema\SchemaParser;

$config = require __DIR__ . '/../config/database.php';
$fresh = in_array('--fresh', $argv, true);

// Stap 1: connectie zonder database, om de database zelf aan te maken.
$serverConnectie = new PDO(
    sprintf('mysql:host=%s;port=%d;charset=%s', $config['host'], $config['port'], $config['charset']),
    $config['username'],
    $config['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
);

$serverConnectie->exec(sprintf(
    "CREATE DATABASE IF NOT EXISTS `%s` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE '%s' */",
    $config['database'],
    $config['collation'],
));

echo "Database '{$config['database']}' is aanwezig.\n";

// Stap 2: connectie mét database, om de tabellen te bouwen.
$connectie = new PDO(
    sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $config['host'], $config['port'], $config['database'], $config['charset']),
    $config['username'],
    $config['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
);

$parser = new SchemaParser();
$builder = new SchemaBuilder($connectie, $config['collation']);

$bestanden = glob(__DIR__ . '/schema/tables/*.xml');
sort($bestanden);

$tabellen = array_map(fn (string $bestand) => $parser->parseBestand($bestand), $bestanden);

if ($fresh) {
    echo "--fresh opgegeven: bestaande tabellen worden gedropt...\n";
    $connectie->exec('SET FOREIGN_KEY_CHECKS = 0');
    foreach (array_reverse($tabellen) as $tabel) {
        $builder->dropTabel($tabel['naam']);
        echo "  gedropt: {$tabel['naam']}\n";
    }
    $connectie->exec('SET FOREIGN_KEY_CHECKS = 1');
}

foreach ($tabellen as $tabel) {
    $builder->bouwTabel($tabel);
    echo "  tabel klaar: {$tabel['naam']}\n";
}

echo "Klaar — " . count($tabellen) . " tabellen verwerkt.\n";
