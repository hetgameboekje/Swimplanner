<?php

declare(strict_types=1);

namespace App\BLL\Models;

enum Rol: string
{
    case Beheerder = 'beheerder';
    case Instructeur = 'instructeur';
}
