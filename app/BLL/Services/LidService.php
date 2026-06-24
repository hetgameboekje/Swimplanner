<?php

declare(strict_types=1);

namespace App\BLL\Services;

use App\BLL\Interfaces\LidRepositoryInterface;
use App\BLL\Models\Lid;

final class LidService
{
    public function __construct(
        private readonly LidRepositoryInterface $lidRepository,
    ) {
    }

    /** @return Lid[] */
    public function ledenVanGroep(int $groepId): array
    {
        return $this->lidRepository->ledenVanGroep($groepId);
    }

    public function toevoegenAanGroep(
        string $voornaam,
        string $achternaam,
        ?string $geboortejaar,
        ?string $contactgegevens,
        int $groepId,
        int $authId,
    ): int {
        $voornaam = $this->valideerNaamdeel($voornaam, 'Voornaam');
        $achternaam = $this->valideerNaamdeel($achternaam, 'Achternaam');
        $geboortejaarGetal = $this->valideerGeboortejaar($geboortejaar);
        $contactgegevens = ($contactgegevens === null || trim($contactgegevens) === '') ? null : trim($contactgegevens);

        return $this->lidRepository->aanmakenEnKoppelen($voornaam, $achternaam, $geboortejaarGetal, $contactgegevens, $groepId, $authId);
    }

    public function uitschrijven(int $groepId, int $lidId, int $authId): void
    {
        $this->lidRepository->uitschrijven($groepId, $lidId, $authId);
    }

    private function valideerNaamdeel(string $waarde, string $veldnaam): string
    {
        $waarde = trim($waarde);
        if ($waarde === '') {
            throw new \InvalidArgumentException("{$veldnaam} is verplicht.");
        }
        if (mb_strlen($waarde) > 100) {
            throw new \InvalidArgumentException("{$veldnaam} mag maximaal 100 tekens zijn.");
        }
        return $waarde;
    }

    private function valideerGeboortejaar(?string $geboortejaar): ?int
    {
        if ($geboortejaar === null || trim($geboortejaar) === '') {
            return null;
        }

        if (!ctype_digit($geboortejaar)) {
            throw new \InvalidArgumentException('Jaartal moet een geheel getal zijn.');
        }

        $jaar = (int) $geboortejaar;
        $huidigJaar = (int) (new \DateTimeImmutable())->format('Y');

        if ($jaar < 1900 || $jaar > $huidigJaar) {
            throw new \InvalidArgumentException("Jaartal moet tussen 1900 en {$huidigJaar} liggen.");
        }

        return $jaar;
    }
}
