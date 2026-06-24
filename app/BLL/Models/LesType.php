<?php

declare(strict_types=1);

namespace App\BLL\Models;

enum LesType: string
{
    case Regulier = 'regulier';
    case Activiteit = 'activiteit';
    case Examen = 'examen';
}
