<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\Core\Controller;

/**
 * DEMO: toont alleen het loginscherm, valideert niets en zet geen sessie.
 * Echte authenticatie volgt in de functionele fase.
 */
final class AuthController extends Controller
{
    public function login(): void
    {
        $this->renderZonderLayout('auth/login');
    }
}
