<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\BLL\Interfaces\MateriaalRepositoryInterface;
use App\BLL\Services\MateriaalService;
use App\Core\Container;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\HuidigeGebruiker;

final class MateriaalController extends Controller
{
    private MateriaalService $materiaalService;

    public function __construct()
    {
        $this->materiaalService = new MateriaalService(Container::maak(MateriaalRepositoryInterface::class));
    }

    public function index(): void
    {
        $this->render('materiaal/index', [
            'materialen' => $this->materiaalService->alleMaterialen(),
        ]);
    }

    public function nieuw(): void
    {
        $this->render('materiaal/form', [
            'materiaal' => null,
            'actiePad' => '/materiaal',
        ]);
    }

    public function opslaan(): void
    {
        try {
            $this->materiaalService->aanmaken(
                (string) ($_POST['naam'] ?? ''),
                (string) ($_POST['categorie'] ?? ''),
                HuidigeGebruiker::id(),
            );
            Flash::zet('success', 'Materiaal aangemaakt.');
        } catch (\InvalidArgumentException $fout) {
            Flash::zet('danger', $fout->getMessage());
        }

        $this->redirect('/materiaal');
    }

    public function bewerken(string $id): void
    {
        $materiaal = $this->materiaalService->zoekOpId((int) $id);
        if ($materiaal === null) {
            http_response_code(404);
            echo 'Materiaal niet gevonden.';
            return;
        }

        $this->render('materiaal/form', [
            'materiaal' => $materiaal,
            'actiePad' => "/materiaal/{$materiaal->id}",
        ]);
    }

    public function bijwerken(string $id): void
    {
        try {
            $this->materiaalService->bijwerken(
                (int) $id,
                (string) ($_POST['naam'] ?? ''),
                (string) ($_POST['categorie'] ?? ''),
                HuidigeGebruiker::id(),
            );
            Flash::zet('success', 'Materiaal bijgewerkt.');
        } catch (\InvalidArgumentException $fout) {
            Flash::zet('danger', $fout->getMessage());
        }

        $this->redirect('/materiaal');
    }

    public function verwijderen(string $id): void
    {
        try {
            $this->materiaalService->verwijderen((int) $id, HuidigeGebruiker::id());
            Flash::zet('success', 'Materiaal verwijderd.');
        } catch (\RuntimeException $fout) {
            Flash::zet('danger', $fout->getMessage());
        }

        $this->redirect('/materiaal');
    }

    public function activerenToggle(string $id): void
    {
        $materiaal = $this->materiaalService->zoekOpId((int) $id);
        if ($materiaal !== null) {
            $this->materiaalService->actiefWijzigen($materiaal->id, !$materiaal->actief, HuidigeGebruiker::id());
            Flash::zet('success', $materiaal->actief ? 'Materiaal gedeactiveerd.' : 'Materiaal geactiveerd.');
        }

        $this->redirect('/materiaal');
    }
}
