<?php

declare(strict_types=1);

namespace App\BLL\Services;

use App\BLL\Interfaces\LesplanningRepositoryInterface;
use App\BLL\Models\Lesplanning;

final class LesplanningService
{
    public function __construct(
        private readonly LesplanningRepositoryInterface $lesplanningRepository,
    ) {
    }

    /** @return Lesplanning[] */
    public function alleLesplanningen(): array
    {
        return $this->lesplanningRepository->alle();
    }

    public function zoekOpId(int $id): ?Lesplanning
    {
        return $this->lesplanningRepository->zoekOpId($id);
    }

    /**
     * @param array<int, array{naam: string, tijd_indicatie: ?string, doel: ?string, activiteit: ?string, organisatie_en_materialen: ?string, didactische_aanwijzingen: ?string, materiaal_ids: int[]}> $onderdelenRuw
     */
    public function aanmaken(
        int $groepId,
        int $instructeurId,
        ?int $lesId,
        string $datum,
        string $beginTijd,
        string $eindTijd,
        ?string $locatie,
        ?string $beginsituatie,
        ?string $doelstelling,
        array $onderdelenRuw,
        int $authId,
    ): int {
        $this->valideerKoppelingen($groepId, $instructeurId);
        $datumObject = $this->valideerDatum($datum);
        $this->valideerTijden($beginTijd, $eindTijd);
        $onderdelen = $this->valideerOnderdelen($onderdelenRuw);

        return $this->lesplanningRepository->aanmaken(
            $groepId,
            $instructeurId,
            $lesId,
            $datumObject,
            $beginTijd,
            $eindTijd,
            $this->leeg($locatie),
            $this->leeg($beginsituatie),
            $this->leeg($doelstelling),
            $onderdelen,
            $authId,
        );
    }

    /**
     * @param array<int, array{naam: string, tijd_indicatie: ?string, doel: ?string, activiteit: ?string, organisatie_en_materialen: ?string, didactische_aanwijzingen: ?string, materiaal_ids: int[]}> $onderdelenRuw
     */
    public function bijwerken(
        int $id,
        int $groepId,
        int $instructeurId,
        ?int $lesId,
        string $datum,
        string $beginTijd,
        string $eindTijd,
        ?string $locatie,
        ?string $beginsituatie,
        ?string $doelstelling,
        array $onderdelenRuw,
        int $authId,
    ): void {
        $this->valideerKoppelingen($groepId, $instructeurId);
        $datumObject = $this->valideerDatum($datum);
        $this->valideerTijden($beginTijd, $eindTijd);
        $onderdelen = $this->valideerOnderdelen($onderdelenRuw);

        $this->lesplanningRepository->bijwerken(
            $id,
            $groepId,
            $instructeurId,
            $lesId,
            $datumObject,
            $beginTijd,
            $eindTijd,
            $this->leeg($locatie),
            $this->leeg($beginsituatie),
            $this->leeg($doelstelling),
            $onderdelen,
            $authId,
        );
    }

    public function verwijderen(int $id, int $authId): void
    {
        $this->lesplanningRepository->verwijderen($id, $authId);
    }

    private function valideerKoppelingen(int $groepId, int $instructeurId): void
    {
        if ($groepId <= 0) {
            throw new \InvalidArgumentException('Kies een groep.');
        }
        if ($instructeurId <= 0) {
            throw new \InvalidArgumentException('Kies een instructeur.');
        }
    }

    private function valideerDatum(string $datum): \DateTimeImmutable
    {
        $object = \DateTimeImmutable::createFromFormat('Y-m-d', $datum);
        if ($object === false) {
            throw new \InvalidArgumentException('Ongeldige datum.');
        }
        return $object;
    }

    private function valideerTijden(string $beginTijd, string $eindTijd): void
    {
        if ($beginTijd === '' || $eindTijd === '') {
            throw new \InvalidArgumentException('Begin- en eindtijd zijn verplicht.');
        }
        if ($beginTijd >= $eindTijd) {
            throw new \InvalidArgumentException('Eindtijd moet na begintijd liggen.');
        }
    }

    /**
     * @param array<int, array{naam: string, tijd_indicatie: ?string, doel: ?string, activiteit: ?string, organisatie_en_materialen: ?string, didactische_aanwijzingen: ?string, materiaal_ids: int[]}> $onderdelenRuw
     * @return array<int, array{naam: string, tijdIndicatie: ?string, doel: ?string, activiteit: ?string, organisatieEnMaterialen: ?string, didactischeAanwijzingen: ?string, materiaalIds: int[]}>
     */
    private function valideerOnderdelen(array $onderdelenRuw): array
    {
        $onderdelen = [];

        foreach ($onderdelenRuw as $onderdeel) {
            $naam = trim((string) ($onderdeel['naam'] ?? ''));
            if ($naam === '') {
                continue;
            }

            $onderdelen[] = [
                'naam' => $naam,
                'tijdIndicatie' => $this->leeg($onderdeel['tijd_indicatie'] ?? null),
                'doel' => $this->leeg($onderdeel['doel'] ?? null),
                'activiteit' => $this->leeg($onderdeel['activiteit'] ?? null),
                'organisatieEnMaterialen' => $this->leeg($onderdeel['organisatie_en_materialen'] ?? null),
                'didactischeAanwijzingen' => $this->leeg($onderdeel['didactische_aanwijzingen'] ?? null),
                'materiaalIds' => array_map('intval', $onderdeel['materiaal_ids'] ?? []),
            ];
        }

        if (empty($onderdelen)) {
            throw new \InvalidArgumentException('Voeg minimaal één lesonderdeel met een naam toe.');
        }

        return $onderdelen;
    }

    private function leeg(?string $waarde): ?string
    {
        return ($waarde === null || trim($waarde) === '') ? null : trim($waarde);
    }
}
