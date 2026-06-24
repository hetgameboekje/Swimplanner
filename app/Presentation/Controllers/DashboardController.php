<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\BLL\Interfaces\GroepRepositoryInterface;
use App\BLL\Interfaces\LesRepositoryInterface;
use App\BLL\Services\GroepService;
use App\BLL\Services\WaarschuwingService;
use App\Core\Container;
use App\Core\Controller;
use App\Core\HuidigeGebruiker;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $groepService = new GroepService(Container::maak(GroepRepositoryInterface::class));
        $waarschuwingService = new WaarschuwingService(Container::maak(LesRepositoryInterface::class));

        $isBeheerder = HuidigeGebruiker::isBeheerder();
        $groepen = $groepService->zichtbareGroepen(HuidigeGebruiker::id(), $isBeheerder);
        $beperkTotGroepIds = $isBeheerder ? null : array_map(static fn ($groep) => $groep->id, $groepen);

        $this->render('dashboard/index', [
            'groepen' => $groepen,
            'waarschuwingen' => $waarschuwingService->actueleWaarschuwingen($beperkTotGroepIds),
        ]);
    }
}
