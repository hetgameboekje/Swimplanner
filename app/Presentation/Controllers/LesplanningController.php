<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\BLL\Interfaces\GroepRepositoryInterface;
use App\BLL\Interfaces\LesplanningRepositoryInterface;
use App\BLL\Interfaces\LesRepositoryInterface;
use App\BLL\Interfaces\MateriaalRepositoryInterface;
use App\BLL\Models\Lesplanning;
use App\BLL\Services\GroepService;
use App\BLL\Services\LesplanningService;
use App\BLL\Services\LesService;
use App\BLL\Services\MateriaalService;
use App\Core\Container;
use App\Core\Controller;

final class LesplanningController extends Controller
{
    public function index(): void
    {
        $lesplanningService = new LesplanningService(Container::maak(LesplanningRepositoryInterface::class));

        $this->render('lesplanningen/index', [
            'lesplanningen' => $lesplanningService->alleLesplanningen(),
        ]);
    }

    public function nieuw(): void
    {
        $groepService = new GroepService(Container::maak(GroepRepositoryInterface::class));
        $materiaalService = new MateriaalService(Container::maak(MateriaalRepositoryInterface::class));

        $vooraf = [
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
                    'groepId' => $les->groepen[0]?->id,
                    'datum' => $les->datum->format('Y-m-d'),
                    'beginTijd' => $les->beginTijd !== null ? substr($les->beginTijd, 0, 5) : null,
                    'eindTijd' => $les->eindTijd !== null ? substr($les->eindTijd, 0, 5) : null,
                    'locatie' => $les->locatie,
                ];
            }
        }

        $this->render('lesplanningen/form', [
            'groepen' => $groepService->alleGroepen(),
            'materialen' => $materiaalService->alleMaterialen(),
            'standaardOnderdelen' => Lesplanning::standaardOnderdeelNamen(),
            'vooraf' => $vooraf,
        ]);
    }
}
