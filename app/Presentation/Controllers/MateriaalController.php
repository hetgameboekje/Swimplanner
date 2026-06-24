<?php

declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\BLL\Interfaces\MateriaalRepositoryInterface;
use App\BLL\Services\MateriaalService;
use App\Core\Container;
use App\Core\Controller;

final class MateriaalController extends Controller
{
    public function index(): void
    {
        $materiaalService = new MateriaalService(Container::maak(MateriaalRepositoryInterface::class));

        $this->render('materiaal/index', [
            'materialen' => $materiaalService->alleMaterialen(),
        ]);
    }
}
