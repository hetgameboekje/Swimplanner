<?php

declare(strict_types=1);

use App\BLL\Interfaces\AfdelingRepositoryInterface;
use App\BLL\Interfaces\GebruikerRepositoryInterface;
use App\BLL\Interfaces\GroepRepositoryInterface;
use App\BLL\Interfaces\LesplanningRepositoryInterface;
use App\BLL\Interfaces\LesRepositoryInterface;
use App\BLL\Interfaces\LidRepositoryInterface;
use App\BLL\Interfaces\MateriaalRepositoryInterface;
use App\Core\Container;
use App\DAL\Fake\FakeLesplanningRepository;
use App\DAL\Fake\FakeMateriaalRepository;
use App\DAL\Pdo\PdoAfdelingRepository;
use App\DAL\Pdo\PdoGebruikerRepository;
use App\DAL\Pdo\PdoGroepRepository;
use App\DAL\Pdo\PdoLesRepository;
use App\DAL\Pdo\PdoLidRepository;

/**
 * Koppelt DAL-interfaces aan implementaties.
 *
 * Groepen/Gebruikers/Afdelingen/Lessen draaien al op de echte database (Pdo*).
 * Lesplanningen/Materiaal staan nog op de Fake in-memory DAL — die module
 * voor module vervangen door een Pdo*-implementatie, zonder dat BLL of
 * Presentation hoeft te wijzigen.
 */
Container::bind(GroepRepositoryInterface::class, static fn () => new PdoGroepRepository());
Container::bind(GebruikerRepositoryInterface::class, static fn () => new PdoGebruikerRepository());
Container::bind(AfdelingRepositoryInterface::class, static fn () => new PdoAfdelingRepository());
Container::bind(LesRepositoryInterface::class, static fn () => new PdoLesRepository(
    Container::maak(GroepRepositoryInterface::class),
    Container::maak(GebruikerRepositoryInterface::class),
));
Container::bind(LidRepositoryInterface::class, static fn () => new PdoLidRepository());

Container::bind(LesplanningRepositoryInterface::class, static fn () => new FakeLesplanningRepository());
Container::bind(MateriaalRepositoryInterface::class, static fn () => new FakeMateriaalRepository());
