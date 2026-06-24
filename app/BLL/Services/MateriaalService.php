<?php

declare(strict_types=1);

namespace App\BLL\Services;

use App\BLL\Interfaces\MateriaalRepositoryInterface;
use App\BLL\Models\Materiaal;

final class MateriaalService
{
    public function __construct(
        private readonly MateriaalRepositoryInterface $materiaalRepository,
    ) {
    }

    /** @return Materiaal[] */
    public function alleMaterialen(): array
    {
        return $this->materiaalRepository->alle();
    }

    public function zoekOpId(int $id): ?Materiaal
    {
        return $this->materiaalRepository->zoekOpId($id);
    }

    public function aanmaken(string $naam, ?string $categorie, int $authId): int
    {
        $naam = $this->valideerNaam($naam);
        return $this->materiaalRepository->aanmaken($naam, $this->leeg($categorie), $authId);
    }

    public function bijwerken(int $id, string $naam, ?string $categorie, int $authId): void
    {
        $naam = $this->valideerNaam($naam);
        $this->materiaalRepository->bijwerken($id, $naam, $this->leeg($categorie), $authId);
    }

    public function verwijderen(int $id, int $authId): void
    {
        try {
            $this->materiaalRepository->verwijderen($id, $authId);
        } catch (\PDOException $fout) {
            if ($fout->getCode() === '23000') {
                throw new \RuntimeException(
                    'Dit materiaal kan niet verwijderd worden: het is nog gekoppeld aan één of meer lesonderdelen. Deactiveer het in plaats daarvan.'
                );
            }
            throw $fout;
        }
    }

    public function actiefWijzigen(int $id, bool $actief, int $authId): void
    {
        $this->materiaalRepository->actiefWijzigen($id, $actief, $authId);
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

    private function leeg(?string $waarde): ?string
    {
        return ($waarde === null || trim($waarde) === '') ? null : trim($waarde);
    }
}
