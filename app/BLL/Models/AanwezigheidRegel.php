<?php

declare(strict_types=1);

namespace App\BLL\Models;

/**
 * Eén rij in het aanwezigheidsregister van een les: een lid van de groep(en)
 * van die les, met de eventueel al geregistreerde status. $status is null
 * zolang er nog niets geregistreerd is voor dit lid bij deze les.
 */
final class AanwezigheidRegel
{
    public function __construct(
        public readonly Lid $lid,
        public readonly ?AanwezigheidStatus $status = null,
        public readonly ?string $opmerking = null,
    ) {
    }
}
