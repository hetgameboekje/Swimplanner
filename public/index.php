<?php

declare(strict_types=1);

session_start();

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/bindings.php';

use App\Core\HuidigeGebruiker;
use App\Core\Router;
use App\Presentation\Controllers\AuthController;
use App\Presentation\Controllers\DashboardController;
use App\Presentation\Controllers\GroepenController;
use App\Presentation\Controllers\LessenController;
use App\Presentation\Controllers\LesplanningController;
use App\Presentation\Controllers\MateriaalController;

$router = new Router();

$router->get('/', DashboardController::class, 'index');
$router->get('/login', AuthController::class, 'login');
$router->post('/login', AuthController::class, 'inloggen');
$router->post('/uitloggen', AuthController::class, 'uitloggen');
$router->get('/groepen', GroepenController::class, 'index');
$router->get('/groepen/nieuw', GroepenController::class, 'nieuw');
$router->post('/groepen', GroepenController::class, 'opslaan');
$router->get('/groepen/{id}/bewerken', GroepenController::class, 'bewerken');
$router->post('/groepen/{id}', GroepenController::class, 'bijwerken');
$router->post('/groepen/{id}/verwijderen', GroepenController::class, 'verwijderen');
$router->post('/groepen/{id}/activeren', GroepenController::class, 'activerenToggle');
$router->get('/groepen/{id}', GroepenController::class, 'tonen');
$router->post('/groepen/{id}/leden', GroepenController::class, 'lidToevoegen');
$router->post('/groepen/{id}/leden/{lidId}/uitschrijven', GroepenController::class, 'lidUitschrijven');
$router->get('/lessen', LessenController::class, 'index');
$router->get('/lessen/nieuw', LessenController::class, 'nieuw');
$router->post('/lessen', LessenController::class, 'opslaan');
$router->get('/lessen/bulk', LessenController::class, 'bulkNieuw');
$router->post('/lessen/bulk', LessenController::class, 'bulkOpslaan');
$router->get('/lessen/{id}/bewerken', LessenController::class, 'bewerken');
$router->post('/lessen/{id}', LessenController::class, 'bijwerken');
$router->post('/lessen/{id}/verwijderen', LessenController::class, 'verwijderen');
$router->get('/lessen/{id}/aanwezigheid', LessenController::class, 'aanwezigheid');
$router->post('/lessen/{id}/aanwezigheid', LessenController::class, 'aanwezigheidOpslaan');
$router->get('/lesplanningen', LesplanningController::class, 'index');
$router->get('/lesplanningen/nieuw', LesplanningController::class, 'nieuw');
$router->post('/lesplanningen', LesplanningController::class, 'opslaan');
$router->get('/lesplanningen/{id}/bewerken', LesplanningController::class, 'bewerken');
$router->post('/lesplanningen/{id}', LesplanningController::class, 'bijwerken');
$router->post('/lesplanningen/{id}/verwijderen', LesplanningController::class, 'verwijderen');
$router->get('/lesplanningen/{id}/print', LesplanningController::class, 'print');
$router->get('/materiaal', MateriaalController::class, 'index');
$router->get('/materiaal/nieuw', MateriaalController::class, 'nieuw');
$router->post('/materiaal', MateriaalController::class, 'opslaan');
$router->get('/materiaal/{id}/bewerken', MateriaalController::class, 'bewerken');
$router->post('/materiaal/{id}', MateriaalController::class, 'bijwerken');
$router->post('/materiaal/{id}/verwijderen', MateriaalController::class, 'verwijderen');
$router->post('/materiaal/{id}/activeren', MateriaalController::class, 'activerenToggle');

$pad = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

// Route-protectie: alles behalve /login vereist een ingelogde gebruiker.
$openbarePaden = ['/login'];
if (!HuidigeGebruiker::isIngelogd() && !in_array($pad, $openbarePaden, true)) {
    header('Location: /login');
    exit;
}

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $pad);
