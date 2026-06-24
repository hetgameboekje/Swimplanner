<?php

declare(strict_types=1);

namespace App\BLL\Services;

use App\BLL\Interfaces\GroepRepositoryInterface;
use App\BLL\Models\Groep;

final class GroepService
{
    public function __construct(
        private readonly GroepRepositoryInterface $groepRepository,
    ) {
    }

    /** @return Groep[] */
    public function alleGroepen(): array
    {
        return $this->groepRepository->alle();
    }

    public function zoekOpId(int $id): ?Groep
    {
        return $this->groepRepository->zoekOpId($id);
    }

    /**
     * Beheerders zien alle groepen; instructeurs zien alleen groepen die
     * ze zelf hebben aangemaakt of waar ze als instructeur aan gekoppeld
     * zijn.
     *
     * @return Groep[]
     */
    public function zichtbareGroepen(int $gebruikerId, bool $isBeheerder): array
    {
        return $isBeheerder ? $this->alleGroepen() : $this->groepRepository->vanInstructeur($gebruikerId);
    }

    /** @param int[] $instructeurIds */
    public function aanmaken(
        string $naam,
        int $afdelingId,
        string $startDatum,
        ?string $eindDatum,
        array $instructeurIds,
        int $authId,
    ): int {
        $naam = $this->valideerNaam($naam);
        $this->valideerAfdeling($afdelingId);
        [$startDatumObject, $eindDatumObject] = $this->valideerPeriode($startDatum, $eindDatum);

        $id = $this->groepRepository->aanmaken($naam, $afdelingId, $startDatumObject, $eindDatumObject, $authId);
        $this->groepRepository->instructeursInstellen($id, $instructeurIds, $authId);

        return $id;
    }

    /** @param int[] $instructeurIds */
    public function bijwerken(
        int $id,
        string $naam,
        int $afdelingId,
        string $startDatum,
        ?string $eindDatum,
        array $instructeurIds,
        int $authId,
    ): void {
        $naam = $this->valideerNaam($naam);
        $this->valideerAfdeling($afdelingId);
        [$startDatumObject, $eindDatumObject] = $this->valideerPeriode($startDatum, $eindDatum);

        $this->groepRepository->bijwerken($id, $naam, $afdelingId, $startDatumObject, $eindDatumObject, $authId);
        $this->groepRepository->instructeursInstellen($id, $instructeurIds, $authId);
    }

    public function verwijderen(int $id, int $authId): void
    {
        try {
            $this->groepRepository->verwijderen($id, $authId);
        } catch (\PDOException $fout) {
            if ($fout->getCode() === '23000') {
                throw new \RuntimeException(
                    'Deze groep kan niet verwijderd worden: er zijn nog lessen, lesplanningen of leden aan gekoppeld. Deactiveer de groep in plaats daarvan.'
                );
            }
            throw $fout;
        }
    }

    public function actiefWijzigen(int $id, bool $actief, int $authId): void
    {
        $this->groepRepository->actiefWijzigen($id, $actief, $authId);
    }

    private function valideerNaam(string $naam): string
    {
        $naam = trim($naam);
        if ($naam === '') {
            throw new \InvalidArgumentException('Naam is verplicht.');
        }
        if (mb_strlen($naam) > 150) {
            throw new \InvalidArgumentException('Naam mag maximaal 150 tekens zijn.');
        }
        return $naam;
    }

    private function valideerAfdeling(int $afdelingId): void
    {
        if ($afdelingId <= 0) {
            throw new \InvalidArgumentException('Kies een afdeling.');
        }
    }

    /** @return array{0: \DateTimeImmutable, 1: ?\DateTimeImmutable} */
    private function valideerPeriode(string $startDatum, ?string $eindDatum): array
    {
        $startDatumObject = \DateTimeImmutable::createFromFormat('Y-m-d', $startDatum);
        if ($startDatumObject === false) {
            throw new \InvalidArgumentException('Ongeldige startdatum.');
        }

        $eindDatumObject = null;
        if ($eindDatum !== null && $eindDatum !== '') {
            $eindDatumObject = \DateTimeImmutable::createFromFormat('Y-m-d', $eindDatum);
            if ($eindDatumObject === false) {
                throw new \InvalidArgumentException('Ongeldige einddatum.');
            }
            if ($eindDatumObject < $startDatumObject) {
                throw new \InvalidArgumentException('Einddatum mag niet vóór de startdatum liggen.');
            }
        }

        return [$startDatumObject, $eindDatumObject];
    }
}
