<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\BLL\Interfaces\AanwezigheidRepositoryInterface;
use App\BLL\Interfaces\GebruikerRepositoryInterface;
use App\BLL\Interfaces\GroepRepositoryInterface;
use App\BLL\Interfaces\LesRepositoryInterface;
use App\BLL\Models\LesType;
use App\BLL\Services\AanwezigheidService;
use App\BLL\Services\GebruikerService;
use App\BLL\Services\GroepService;
use App\BLL\Services\LesService;
use App\Core\Container;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\HuidigeGebruiker;

final class LessenController extends Controller
{
    private LesService $lesService;
    private GroepService $groepService;
    private GebruikerService $gebruikerService;
    private AanwezigheidService $aanwezigheidService;

    public function __construct()
    {
        $this->lesService = new LesService(Container::maak(LesRepositoryInterface::class));
        $this->groepService = new GroepService(Container::maak(GroepRepositoryInterface::class));
        $this->gebruikerService = new GebruikerService(Container::maak(GebruikerRepositoryInterface::class));
        $this->aanwezigheidService = new AanwezigheidService(Container::maak(AanwezigheidRepositoryInterface::class));
    }

    public function index(): void
    {
        $this->render('lessen/index', [
            'lessen' => $this->lesService->alleLessen(),
        ]);
    }

    public function nieuw(): void
    {
        $this->render('lessen/form', [
            'les' => null,
            'groepen' => $this->groepService->alleGroepen(),
            'instructeurs' => $this->gebruikerService->alleInstructeurs(),
            'lesTypes' => LesType::cases(),
            'actiePad' => '/lessen',
            'geselecteerdeGroepIds' => [],
            'geselecteerdeInstructeurIds' => [],
        ]);
    }

    public function opslaan(): void
    {
        try {
            $this->lesService->aanmaken(
                array_map('intval', $_POST['groep_ids'] ?? []),
                (string) ($_POST['datum'] ?? ''),
                (string) ($_POST['type'] ?? ''),
                array_map('intval', $_POST['instructeur_ids'] ?? []),
                $this->leegAlsNull($_POST['begin_tijd'] ?? null),
                $this->leegAlsNull($_POST['eind_tijd'] ?? null),
                $this->leegAlsNull($_POST['locatie'] ?? null),
                HuidigeGebruiker::id(),
            );
            Flash::zet('success', 'Les aangemaakt.');
        } catch (\InvalidArgumentException $fout) {
            Flash::zet('danger', $fout->getMessage());
        }

        $this->redirect('/lessen');
    }

    public function bewerken(string $id): void
    {
        $les = $this->lesService->zoekOpId((int) $id);
        if ($les === null) {
            http_response_code(404);
            echo 'Les niet gevonden.';
            return;
        }

        $this->render('lessen/form', [
            'les' => $les,
            'groepen' => $this->groepService->alleGroepen(),
            'instructeurs' => $this->gebruikerService->alleInstructeurs(),
            'lesTypes' => LesType::cases(),
            'actiePad' => "/lessen/{$les->id}",
            'geselecteerdeGroepIds' => array_map(static fn ($groep) => $groep->id, $les->groepen),
            'geselecteerdeInstructeurIds' => array_map(static fn ($instructeur) => $instructeur->id, $les->instructeurs),
        ]);
    }

    public function bijwerken(string $id): void
    {
        try {
            $this->lesService->bijwerken(
                (int) $id,
                array_map('intval', $_POST['groep_ids'] ?? []),
                (string) ($_POST['datum'] ?? ''),
                (string) ($_POST['type'] ?? ''),
                array_map('intval', $_POST['instructeur_ids'] ?? []),
                $this->leegAlsNull($_POST['begin_tijd'] ?? null),
                $this->leegAlsNull($_POST['eind_tijd'] ?? null),
                $this->leegAlsNull($_POST['locatie'] ?? null),
                HuidigeGebruiker::id(),
            );
            Flash::zet('success', 'Les bijgewerkt.');
        } catch (\InvalidArgumentException $fout) {
            Flash::zet('danger', $fout->getMessage());
        }

        $this->redirect('/lessen');
    }

    public function verwijderen(string $id): void
    {
        $this->lesService->verwijderen((int) $id, HuidigeGebruiker::id());
        Flash::zet('success', 'Les verwijderd.');

        $this->redirect('/lessen');
    }

    public function bulkNieuw(): void
    {
        $this->render('lessen/bulk', [
            'groepen' => $this->groepService->alleGroepen(),
            'instructeurs' => $this->gebruikerService->alleInstructeurs(),
            'lesTypes' => LesType::cases(),
            'vandaag' => (new \DateTimeImmutable())->format('Y-m-d'),
        ]);
    }

    public function bulkOpslaan(): void
    {
        try {
            $idsAangemaakt = $this->lesService->aanmakenBulk(
                array_map('intval', $_POST['groep_ids'] ?? []),
                (string) ($_POST['start_datum'] ?? ''),
                (int) ($_POST['aantal_lessen'] ?? 0),
                (int) ($_POST['interval_dagen'] ?? 7),
                (string) ($_POST['type'] ?? ''),
                array_map('intval', $_POST['instructeur_ids'] ?? []),
                $this->leegAlsNull($_POST['begin_tijd'] ?? null),
                $this->leegAlsNull($_POST['eind_tijd'] ?? null),
                $this->leegAlsNull($_POST['locatie'] ?? null),
                HuidigeGebruiker::id(),
            );
            Flash::zet('success', count($idsAangemaakt) . ' lessen aangemaakt.');
        } catch (\InvalidArgumentException $fout) {
            Flash::zet('danger', $fout->getMessage());
        }

        $this->redirect('/lessen');
    }

    public function aanwezigheid(string $id): void
    {
        $les = $this->lesService->zoekOpId((int) $id);
        if ($les === null) {
            http_response_code(404);
            echo 'Les niet gevonden.';
            return;
        }

        $this->render('lessen/aanwezigheid', [
            'les' => $les,
            'regels' => $this->aanwezigheidService->voorLes($les->id),
        ]);
    }

    public function aanwezigheidOpslaan(string $id): void
    {
        $statussen = $_POST['status'] ?? [];
        $opmerkingen = $_POST['opmerking'] ?? [];

        $ruweRegels = [];
        foreach ($statussen as $lidId => $status) {
            $ruweRegels[$lidId] = [
                'status' => $status,
                'opmerking' => $opmerkingen[$lidId] ?? '',
            ];
        }

        try {
            $this->aanwezigheidService->opslaan((int) $id, $ruweRegels, HuidigeGebruiker::id());
            Flash::zet('success', 'Aanwezigheid opgeslagen.');
        } catch (\InvalidArgumentException $fout) {
            Flash::zet('danger', $fout->getMessage());
        }

        $this->redirect("/lessen/{$id}/aanwezigheid");
    }

    private function leegAlsNull(?string $waarde): ?string
    {
        return ($waarde === null || $waarde === '') ? null : $waarde;
    }
}
