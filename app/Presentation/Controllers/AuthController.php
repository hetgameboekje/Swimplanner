<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\BLL\Interfaces\GebruikerRepositoryInterface;
use App\BLL\Services\AuthService;
use App\Core\AuditLogger;
use App\Core\Container;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\HuidigeGebruiker;

final class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService(Container::maak(GebruikerRepositoryInterface::class));
    }

    public function login(): void
    {
        if (HuidigeGebruiker::isIngelogd()) {
            $this->redirect('/');
        }
        $this->renderZonderLayout('auth/login');
    }

    public function inloggen(): void
    {
        $gebruiker = $this->authService->inloggen(
            (string) ($_POST['email'] ?? ''),
            (string) ($_POST['wachtwoord'] ?? ''),
        );

        if ($gebruiker === null) {
            Flash::zet('danger', 'E-mailadres of wachtwoord onjuist.');
            $this->redirect('/login');
        }

        HuidigeGebruiker::inloggen($gebruiker);
        AuditLogger::log($gebruiker->id, 'login', 'gebruiker', $gebruiker->id, 'Ingelogd.');

        $this->redirect('/');
    }

    public function uitloggen(): void
    {
        if (HuidigeGebruiker::isIngelogd()) {
            $authId = HuidigeGebruiker::id();
            AuditLogger::log($authId, 'logout', 'gebruiker', $authId, 'Uitgelogd.');
        }

        HuidigeGebruiker::uitloggen();
        Flash::zet('success', 'Je bent uitgelogd.');
        $this->redirect('/login');
    }
}
