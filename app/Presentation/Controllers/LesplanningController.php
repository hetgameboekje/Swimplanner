<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\BLL\Interfaces\GebruikerRepositoryInterface;
use App\BLL\Interfaces\GroepRepositoryInterface;
use App\BLL\Interfaces\LesplanningRepositoryInterface;
use App\BLL\Interfaces\LesRepositoryInterface;
use App\BLL\Interfaces\MateriaalRepositoryInterface;
use App\BLL\Models\Lesplanning;
use App\BLL\Services\GebruikerService;
use App\BLL\Services\GroepService;
use App\BLL\Services\LesplanningService;
use App\BLL\Services\LesService;
use App\BLL\Services\MateriaalService;
use App\Core\Container;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\HuidigeGebruiker;

final class LesplanningController extends Controller
{
    private LesplanningService $lesplanningService;
    private GroepService $groepService;
    private GebruikerService $gebruikerService;
    private MateriaalService $materiaalService;

    public function __construct()
    {
        $this->lesplanningService = new LesplanningService(Container::maak(LesplanningRepositoryInterface::class));
        $this->groepService = new GroepService(Container::maak(GroepRepositoryInterface::class));
        $this->gebruikerService = new GebruikerService(Container::maak(GebruikerRepositoryInterface::class));
        $this->materiaalService = new MateriaalService(Container::maak(MateriaalRepositoryInterface::class));
    }

    public function index(): void
    {
        $this->render('lesplanningen/index', [
            'lesplanningen' => $this->lesplanningService->alleLesplanningen(),
        ]);
    }

    public function nieuw(): void
    {
        $vooraf = [
            'lesId' => null,
            'groepId' => null,
            'datum' => null,
            'beginTijd' => null,
            'eindTijd' => null,
            'locatie' => null,
        ];

        $lesId = $_GET['les_id'] ?? null;
        if ($lesId !== null) {
            $lesService = new LesService(Container::maak(LesRepositoryInterface::class));
            $les = $lesService->zoekOpId((int) $lesId);

            if ($les !== null) {
                $vooraf = [
                    'lesId' => $les->id,
                    'groepId' => $les->groepen[0]?->id,
                    'datum' => $les->datum->format('Y-m-d'),
                    'beginTijd' => $les->beginTijd !== null ? substr($les->beginTijd, 0, 5) : null,
                    'eindTijd' => $les->eindTijd !== null ? substr($les->eindTijd, 0, 5) : null,
                    'locatie' => $les->locatie,
                ];
            }
        }

        $this->render('lesplanningen/form', [
            'lesplanning' => null,
            'groepen' => $this->groepService->alleGroepen(),
            'instructeurs' => $this->gebruikerService->alleInstructeurs(),
            'materialen' => $this->materiaalService->alleMaterialen(),
            'standaardOnderdelen' => Lesplanning::standaardOnderdeelNamen(),
            'vooraf' => $vooraf,
            'geselecteerdeInstructeurId' => HuidigeGebruiker::id(),
            'actiePad' => '/lesplanningen',
        ]);
    }

    public function opslaan(): void
    {
        try {
            $lesIdRuw = (string) ($_POST['les_id'] ?? '');
            $this->lesplanningService->aanmaken(
                (int) ($_POST['groep_id'] ?? 0),
                (int) ($_POST['instructeur_id'] ?? 0),
                $lesIdRuw !== '' ? (int) $lesIdRuw : null,
                (string) ($_POST['datum'] ?? ''),
                (string) ($_POST['begin_tijd'] ?? ''),
                (string) ($_POST['eind_tijd'] ?? ''),
                (string) ($_POST['locatie'] ?? ''),
                (string) ($_POST['beginsituatie'] ?? ''),
                (string) ($_POST['doelstelling'] ?? ''),
                $_POST['onderdelen'] ?? [],
                HuidigeGebruiker::id(),
            );
            Flash::zet('success', 'Lesplanning aangemaakt.');
            $this->redirect('/lesplanningen');
        } catch (\InvalidArgumentException $fout) {
            Flash::zet('danger', $fout->getMessage());
            $this->redirect('/lesplanningen/nieuw');
        }
    }

    public function bewerken(string $id): void
    {
        $lesplanning = $this->lesplanningService->zoekOpId((int) $id);
        if ($lesplanning === null) {
            http_response_code(404);
            echo 'Lesplanning niet gevonden.';
            return;
        }

        $this->render('lesplanningen/form', [
            'lesplanning' => $lesplanning,
            'groepen' => $this->groepService->alleGroepen(),
            'instructeurs' => $this->gebruikerService->alleInstructeurs(),
            'materialen' => $this->materiaalService->alleMaterialen(),
            'standaardOnderdelen' => Lesplanning::standaardOnderdeelNamen(),
            'vooraf' => [
                'lesId' => $lesplanning->lesId,
                'groepId' => $lesplanning->groep->id,
                'datum' => $lesplanning->datum->format('Y-m-d'),
                'beginTijd' => $lesplanning->beginTijd,
                'eindTijd' => $lesplanning->eindTijd,
                'locatie' => $lesplanning->locatie,
            ],
            'geselecteerdeInstructeurId' => $lesplanning->instructeur->id,
            'actiePad' => "/lesplanningen/{$lesplanning->id}",
        ]);
    }

    public function bijwerken(string $id): void
    {
        try {
            $lesIdRuw = (string) ($_POST['les_id'] ?? '');
            $this->lesplanningService->bijwerken(
                (int) $id,
                (int) ($_POST['groep_id'] ?? 0),
                (int) ($_POST['instructeur_id'] ?? 0),
                $lesIdRuw !== '' ? (int) $lesIdRuw : null,
                (string) ($_POST['datum'] ?? ''),
                (string) ($_POST['begin_tijd'] ?? ''),
                (string) ($_POST['eind_tijd'] ?? ''),
                (string) ($_POST['locatie'] ?? ''),
                (string) ($_POST['beginsituatie'] ?? ''),
                (string) ($_POST['doelstelling'] ?? ''),
                $_POST['onderdelen'] ?? [],
                HuidigeGebruiker::id(),
            );
            Flash::zet('success', 'Lesplanning bijgewerkt.');
            $this->redirect('/lesplanningen');
        } catch (\InvalidArgumentException $fout) {
            Flash::zet('danger', $fout->getMessage());
            $this->redirect("/lesplanningen/{$id}/bewerken");
        }
    }

    public function verwijderen(string $id): void
    {
        $this->lesplanningService->verwijderen((int) $id, HuidigeGebruiker::id());
        Flash::zet('success', 'Lesplanning verwijderd.');

        $this->redirect('/lesplanningen');
    }
}
