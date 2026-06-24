<?php

declare(strict_types=1);

namespace App\BLL\Models;

enum Rol: string
{
    case Beheerder = 'beheerder';
    case Instructeur = 'instructeur';
}

final class Gebruiker
{
    public function __construct(
        public readonly int $id,
        public readonly string $naam,
        public readonly string $email,
        public readonly Rol $rol,
    ) {
    }
}
