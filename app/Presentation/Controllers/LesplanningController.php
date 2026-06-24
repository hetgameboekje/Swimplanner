<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\BLL\Interfaces\GroepRepositoryInterface;
use App\BLL\Interfaces\LesplanningRepositoryInterface;
use App\BLL\Interfaces\MateriaalRepositoryInterface;
use App\BLL\Models\Lesplanning;
use App\BLL\Services\GroepService;
use App\BLL\Services\LesplanningService;
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

        $this->render('lesplanningen/form', [
            'groepen' => $groepService->alleGroepen(),
            'materialen' => $materiaalService->alleMaterialen(),
            'standaardOnderdelen' => Lesplanning::standaardOnderdeelNamen(),
        ]);
    }
}
