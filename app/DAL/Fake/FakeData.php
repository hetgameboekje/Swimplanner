<?php

declare(strict_types=1);

namespace App\DAL\Fake;

use App\BLL\Models\Afdeling;
use App\BLL\Models\Gebruiker;
use App\BLL\Models\Groep;
use App\BLL\Models\Les;
use App\BLL\Models\LesType;
use App\BLL\Models\Lesplanning;
use App\BLL\Models\LesplanningOnderdeel;
use App\BLL\Models\Materiaal;
use App\BLL\Models\Rol;

/**
 * Eén centrale plek met demo-data zodat alle Fake-repositories consistent
 * naar dezelfde objecten verwijzen. Wordt niet hergebruikt zodra de echte
 * DAL (PDO) er is.
 */
final class FakeData
{
    public static function instructeur(): Gebruiker
    {
        static $instructeur = null;
        return $instructeur ??= new Gebruiker(1, 'Timo Bergthaler', 'timo@example.nl', Rol::Instructeur);
    }

    public static function afdelingZwemmendRedden(): Afdeling
    {
        static $afdeling = null;
        return $afdeling ??= new Afdeling(1, 'Zwemmend redden');
    }

    /** @return Groep[] */
    public static function groepen(): array
    {
        static $groepen = null;
        if ($groepen !== null) {
            return $groepen;
        }

        $afdeling = self::afdelingZwemmendRedden();
        $instructeur = self::instructeur();

        return $groepen = [
            new Groep(1, 'Zwemmend Redder 1', $afdeling, [$instructeur], 14),
            new Groep(2, 'Zwemmend Redder 2', $afdeling, [$instructeur], 11),
            new Groep(3, 'Junior Redder', $afdeling, [$instructeur], 9),
        ];
    }

    /** @return Les[] */
    public static function lessen(): array
    {
        static $lessen = null;
        if ($lessen !== null) {
            return $lessen;
        }

        $groepen = self::groepen();
        $instructeur = self::instructeur();

        return $lessen = [
            new Les(1, $groepen[0], new \DateTimeImmutable('next sunday'), LesType::Regulier, $instructeur, true),
            new Les(2, $groepen[1], new \DateTimeImmutable('next sunday +7 days'), LesType::Regulier, $instructeur, false),
            new Les(3, $groepen[2], new \DateTimeImmutable('next sunday +14 days'), LesType::Examen, $instructeur, false),
        ];
    }

    /** @return Materiaal[] */
    public static function materialen(): array
    {
        static $materialen = null;
        if ($materialen !== null) {
            return $materialen;
        }

        return $materialen = [
            new Materiaal(1, 'Reddingsvest', 'Reddingsmateriaal'),
            new Materiaal(2, 'Reddingsboei (rescue tube)', 'Reddingsmateriaal'),
            new Materiaal(3, 'Duikbril', 'Zwemmateriaal'),
            new Materiaal(4, 'Drijfstok', 'Zwemmateriaal'),
            new Materiaal(5, 'Pop (oefenpop)', 'Reddingsmateriaal'),
        ];
    }

    /** @return Lesplanning[] */
    public static function lesplanningen(): array
    {
        static $lesplanningen = null;
        if ($lesplanningen !== null) {
            return $lesplanningen;
        }

        $groepen = self::groepen();
        $instructeur = self::instructeur();
        $materialen = self::materialen();

        $onderdelen = [
            new LesplanningOnderdeel(1, 'Inleiding', '10 min', 'Opwarmen en uitleg lesdoel', 'Vrij zwemmen + korte instructie', 'Hele bad, geen materiaal', 'Klassikaal', []),
            new LesplanningOnderdeel(2, 'Kern 1', '20 min', 'Drenkeling benaderen en vastpakken', 'Oefenen kopgreep in tweetallen', '2 banen, pop per groepje', 'Demonstratie + nadoen', [$materialen[4]]),
            new LesplanningOnderdeel(3, 'Kern 2', '20 min', 'Vervoeren van een drenkeling', '25m vervoeren in houdgreep', '2 banen', 'Korte feedbackronde per koppel', [$materialen[4], $materialen[1]]),
            new LesplanningOnderdeel(4, 'Afsluiting', '10 min', 'Terugblik en afkoelen', 'Vrij zwemmen + evaluatie', 'Hele bad', 'Klassikaal gesprek', []),
        ];

        return $lesplanningen = [
            new Lesplanning(
                1,
                $groepen[0],
                $instructeur,
                new \DateTimeImmutable('next sunday'),
                '10:00',
                '11:00',
                'Zwembad De Roerdomp, baan 3-4',
                'Groep kent de basistechnieken van drenkeling benaderen nog niet.',
                'Aan het einde van de les kan de zwemmer een drenkeling op een veilige manier benaderen en vervoeren.',
                $onderdelen,
            ),
        ];
    }
}
