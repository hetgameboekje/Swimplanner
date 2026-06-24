<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\BLL\Interfaces\AanwezigheidRepositoryInterface;
use App\BLL\Interfaces\GroepRepositoryInterface;
use App\BLL\Interfaces\LesRepositoryInterface;
use App\BLL\Interfaces\LidRepositoryInterface;
use App\BLL\Services\AanwezigheidOverzichtService;
use App\BLL\Services\AanwezigheidService;
use App\BLL\Services\GroepService;
use App\BLL\Services\LesService;
use App\BLL\Services\LidService;
use App\Core\Container;
use App\Core\Controller;
use App\Core\HuidigeGebruiker;

final class AanwezigheidController extends Controller
{
    public function overzicht(): void
    {
        $groepService = new GroepService(Container::maak(GroepRepositoryInterface::class));
        $overzichtService = new AanwezigheidOverzichtService(
            new LesService(Container::maak(LesRepositoryInterface::class)),
            new LidService(Container::maak(LidRepositoryInterface::class)),
            new AanwezigheidService(Container::maak(AanwezigheidRepositoryInterface::class)),
        );

        $groepen = $groepService->zichtbareGroepen(HuidigeGebruiker::id(), HuidigeGebruiker::isBeheerder());

        $this->render('aanwezigheid/overzicht', [
            'overzicht' => $overzichtService->voorGroepen($groepen),
        ]);
    }
}
