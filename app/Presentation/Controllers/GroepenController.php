<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\BLL\Interfaces\GroepRepositoryInterface;
use App\BLL\Services\GroepService;
use App\Core\Container;
use App\Core\Controller;

final class GroepenController extends Controller
{
    public function index(): void
    {
        $groepService = new GroepService(Container::maak(GroepRepositoryInterface::class));

        $this->render('groepen/index', [
            'groepen' => $groepService->alleGroepen(),
        ]);
    }
}
