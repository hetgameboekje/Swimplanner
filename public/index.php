<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/bindings.php';

use App\Core\Router;
use App\Presentation\Controllers\AuthController;
use App\Presentation\Controllers\DashboardController;
use App\Presentation\Controllers\GroepenController;
use App\Presentation\Controllers\LesplanningController;
use App\Presentation\Controllers\MateriaalController;

$router = new Router();

$router->get('/', DashboardController::class, 'index');
$router->get('/login', AuthController::class, 'login');
$router->get('/groepen', GroepenController::class, 'index');
$router->get('/lesplanningen', LesplanningController::class, 'index');
$router->get('/lesplanningen/nieuw', LesplanningController::class, 'nieuw');
$router->get('/materiaal', MateriaalController::class, 'index');

$pad = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $pad);
