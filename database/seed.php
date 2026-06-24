<?php

declare(strict_types=1);

/**
 * Idempotente basisdata: zonder dit zijn er geen geldige FK-doelen voor
 * groepen.afdeling_id / created_by. Draai na database/build.php:
 *
 *   php database/seed.php
 */

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/database.php';

$connectie = new PDO(
    sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $config['host'], $config['port'], $config['database'], $config['charset']),
    $config['username'],
    $config['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
);

$connectie->exec("INSERT IGNORE INTO afdelingen (naam, actief) VALUES ('Zwemmend redden', 1)");
echo "Afdeling 'Zwemmend redden' aanwezig.\n";

$gebruikers = [
    ['naam' => 'Timo Bergthaler', 'email' => 'timo@reddingsbrigade.nl', 'rol' => 'beheerder'],
    ['naam' => 'Anna de Vries', 'email' => 'anna@reddingsbrigade.nl', 'rol' => 'instructeur'],
    ['naam' => 'Sven Janssen', 'email' => 'sven@reddingsbrigade.nl', 'rol' => 'instructeur'],
];

$insert = $connectie->prepare(
    'INSERT IGNORE INTO gebruikers (naam, email, wachtwoord_hash, rol, actief) VALUES (:naam, :email, :hash, :rol, 1)'
);

foreach ($gebruikers as $gebruiker) {
    $insert->execute([
        'naam' => $gebruiker['naam'],
        'email' => $gebruiker['email'],
        'hash' => password_hash('demo123', PASSWORD_DEFAULT),
        'rol' => $gebruiker['rol'],
    ]);
    echo "Gebruiker '{$gebruiker['naam']}' aanwezig.\n";
}

echo "Seed klaar.\n";
