<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\BLL\Interfaces\AfdelingRepositoryInterface;
use App\BLL\Interfaces\GebruikerRepositoryInterface;
use App\BLL\Interfaces\GroepRepositoryInterface;
use App\BLL\Services\AfdelingService;
use App\BLL\Services\GebruikerService;
use App\BLL\Services\GroepService;
use App\Core\Container;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\HuidigeGebruiker;

final class GroepenController extends Controller
{
    private GroepService $groepService;
    private GebruikerService $gebruikerService;
    private AfdelingService $afdelingService;

    public function __construct()
    {
        $this->groepService = new GroepService(Container::maak(GroepRepositoryInterface::class));
        $this->gebruikerService = new GebruikerService(Container::maak(GebruikerRepositoryInterface::class));
        $this->afdelingService = new AfdelingService(Container::maak(AfdelingRepositoryInterface::class));
    }

    public function index(): void
    {
        $this->render('groepen/index', [
            'groepen' => $this->groepService->alleGroepen(),
        ]);
    }

    public function nieuw(): void
    {
        $this->render('groepen/form', [
            'groep' => null,
            'afdelingen' => $this->afdelingService->alleActief(),
            'instructeurs' => $this->gebruikerService->alleInstructeurs(),
            'geselecteerdeInstructeurIds' => [],
            'actiePad' => '/groepen',
            'vandaag' => (new \DateTimeImmutable())->format('Y-m-d'),
        ]);
    }

    public function opslaan(): void
    {
        try {
            $eindDatum = (string) ($_POST['eind_datum'] ?? '');
            $this->groepService->aanmaken(
                (string) ($_POST['naam'] ?? ''),
                (int) ($_POST['afdeling_id'] ?? 0),
                (string) ($_POST['start_datum'] ?? ''),
                $eindDatum !== '' ? $eindDatum : null,
                array_map('intval', $_POST['instructeur_ids'] ?? []),
                HuidigeGebruiker::id(),
            );
            Flash::zet('success', 'Groep aangemaakt.');
        } catch (\InvalidArgumentException $fout) {
            Flash::zet('danger', $fout->getMessage());
        }

        $this->redirect('/groepen');
    }

    public function bewerken(string $id): void
    {
        $groep = $this->groepService->zoekOpId((int) $id);
        if ($groep === null) {
            http_response_code(404);
            echo 'Groep niet gevonden.';
            return;
        }

        $this->render('groepen/form', [
            'groep' => $groep,
            'afdelingen' => $this->afdelingService->alleActief(),
            'instructeurs' => $this->gebruikerService->alleInstructeurs(),
            'geselecteerdeInstructeurIds' => array_map(static fn ($instructeur) => $instructeur->id, $groep->instructeurs),
            'actiePad' => "/groepen/{$groep->id}",
            'vandaag' => (new \DateTimeImmutable())->format('Y-m-d'),
        ]);
    }

    public function bijwerken(string $id): void
    {
        try {
            $eindDatum = (string) ($_POST['eind_datum'] ?? '');
            $this->groepService->bijwerken(
                (int) $id,
                (string) ($_POST['naam'] ?? ''),
                (int) ($_POST['afdeling_id'] ?? 0),
                (string) ($_POST['start_datum'] ?? ''),
                $eindDatum !== '' ? $eindDatum : null,
                array_map('intval', $_POST['instructeur_ids'] ?? []),
                HuidigeGebruiker::id(),
            );
            Flash::zet('success', 'Groep bijgewerkt.');
        } catch (\InvalidArgumentException $fout) {
            Flash::zet('danger', $fout->getMessage());
        }

        $this->redirect('/groepen');
    }

    public function verwijderen(string $id): void
    {
        try {
            $this->groepService->verwijderen((int) $id, HuidigeGebruiker::id());
            Flash::zet('success', 'Groep verwijderd.');
        } catch (\RuntimeException $fout) {
            Flash::zet('danger', $fout->getMessage());
        }

        $this->redirect('/groepen');
    }

    public function activerenToggle(string $id): void
    {
        $groep = $this->groepService->zoekOpId((int) $id);
        if ($groep !== null) {
            $this->groepService->actiefWijzigen($groep->id, !$groep->actief, HuidigeGebruiker::id());
            Flash::zet('success', $groep->actief ? 'Groep gedeactiveerd.' : 'Groep geactiveerd.');
        }

        $this->redirect('/groepen');
    }
}
