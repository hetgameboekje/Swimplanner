<?php

declare(strict_types=1);

use App\BLL\Interfaces\GroepRepositoryInterface;
use App\BLL\Interfaces\LesplanningRepositoryInterface;
use App\BLL\Interfaces\LesRepositoryInterface;
use App\BLL\Interfaces\MateriaalRepositoryInterface;
use App\Core\Container;
use App\DAL\Fake\FakeGroepRepository;
use App\DAL\Fake\FakeLesplanningRepository;
use App\DAL\Fake\FakeLesRepository;
use App\DAL\Fake\FakeMateriaalRepository;

/**
 * Koppelt DAL-interfaces aan implementaties. Vandaag: Fake (in-memory demo).
 * Zodra de echte database er is, vervang je hier de Fake*-klassen door de
 * PDO-implementaties — BLL en Presentation hoeven niet te wijzigen.
 */
Container::bind(GroepRepositoryInterface::class, static fn () => new FakeGroepRepository());
Container::bind(LesRepositoryInterface::class, static fn () => new FakeLesRepository());
Container::bind(LesplanningRepositoryInterface::class, static fn () => new FakeLesplanningRepository());
Container::bind(MateriaalRepositoryInterface::class, static fn () => new FakeMateriaalRepository());
