<?php
/** @var \App\BLL\Models\Lesplanning|null $lesplanning */
/** @var \App\BLL\Models\Groep[] $groepen */
/** @var \App\BLL\Models\Gebruiker[] $instructeurs */
/** @var \App\BLL\Models\Materiaal[] $materialen */
/** @var string[] $standaardOnderdelen */
/** @var array{lesId: ?int, groepId: ?int, datum: ?string, beginTijd: ?string, eindTijd: ?string, locatie: ?string} $vooraf */
/** @var int $geselecteerdeInstructeurId */
/** @var string $actiePad */

/**
 * @return array<int, array{naam: string, tijdIndicatie: string, doel: string, activiteit: string, organisatieEnMaterialen: string, didactischeAanwijzingen: string, materiaalIds: int[]}>
 */
$onderdeelRijen = $lesplanning !== null
    ? array_map(static fn ($o) => [
        'naam' => $o->naam,
        'tijdIndicatie' => $o->tijdIndicatie,
        'doel' => $o->doel,
        'activiteit' => $o->activiteit,
        'organisatieEnMaterialen' => $o->organisatieEnMaterialen,
        'didactischeAanwijzingen' => $o->didactischeAanwijzingen,
        'materiaalIds' => array_map(static fn ($m) => $m->id, $o->materialen),
    ], $lesplanning->onderdelen)
    : array_map(static fn (string $naam) => [
        'naam' => $naam,
        'tijdIndicatie' => '',
        'doel' => '',
        'activiteit' => '',
        'organisatieEnMaterialen' => '',
        'didactischeAanwijzingen' => '',
        'materiaalIds' => [],
    ], $standaardOnderdelen);
?>
<h1 class="h4 mb-3"><?= $lesplanning === null ? 'Nieuwe lesplanning' : 'Lesplanning bewerken' ?></h1>

<?php if ($vooraf['groepId'] !== null && $lesplanning === null): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Gegevens overgenomen vanuit de bijbehorende les.
    </div>
<?php endif; ?>

<form method="post" action="<?= htmlspecialchars($actiePad) ?>">
    <input type="hidden" name="les_id" value="<?= htmlspecialchars((string) ($vooraf['lesId'] ?? '')) ?>">

    <div class="card mb-3">
        <div class="card-header">Gegevens les</div>
        <div class="card-body row g-3">
            <div class="col-md-4">
                <label class="form-label">Instructeur</label>
                <select name="instructeur_id" class="form-select" required>
                    <?php foreach ($instructeurs as $instructeur): ?>
                        <option value="<?= $instructeur->id ?>" <?= $geselecteerdeInstructeurId === $instructeur->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($instructeur->naam) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Groep / doelgroep</label>
                <select name="groep_id" class="form-select" required>
                    <?php foreach ($groepen as $groep): ?>
                        <option value="<?= $groep->id ?>" <?= $vooraf['groepId'] === $groep->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($groep->naam) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Locatie</label>
                <input type="text" name="locatie" class="form-control" placeholder="bv. Zwembad De Roerdomp, baan 3-4"
                       value="<?= htmlspecialchars($vooraf['locatie'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Datum</label>
                <input type="date" name="datum" class="form-control" required value="<?= htmlspecialchars($vooraf['datum'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Begintijd</label>
                <input type="time" name="begin_tijd" class="form-control" required value="<?= htmlspecialchars($vooraf['beginTijd'] ?? '10:00') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Eindtijd</label>
                <input type="time" name="eind_tijd" class="form-control" required value="<?= htmlspecialchars($vooraf['eindTijd'] ?? '11:00') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Beginsituatie</label>
                <textarea name="beginsituatie" class="form-control" rows="2" placeholder="Wat kan de groep al, waar wordt op aangesloten?"><?= htmlspecialchars($lesplanning?->beginsituatie ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Doelstelling</label>
                <textarea name="doelstelling" class="form-control" rows="2" placeholder="Wat moet de zwemmer aan het einde van de les kunnen?"><?= htmlspecialchars($lesplanning?->doelstelling ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Lesonderdelen</span>
            <button type="button" id="onderdeel-toevoegen" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-plus-lg"></i> Kern-onderdeel toevoegen
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered lvf-table mb-0" id="lesplanning-onderdelen">
                    <thead class="table-light">
                    <tr>
                        <th style="width: 3%">#</th>
                        <th style="width: 12%">Onderdeel</th>
                        <th style="width: 8%">Tijd</th>
                        <th>Doel van de oefening/activiteit</th>
                        <th>Oefening / activiteit</th>
                        <th>Organisatie en materialen</th>
                        <th>Didactische aanwijzingen / werkvorm</th>
                        <th style="width: 3%"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($onderdeelRijen as $i => $onderdeel): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><input type="text" name="onderdelen[<?= $i ?>][naam]" class="form-control form-control-sm" value="<?= htmlspecialchars($onderdeel['naam']) ?>"></td>
                            <td><input type="text" name="onderdelen[<?= $i ?>][tijd_indicatie]" class="form-control form-control-sm" placeholder="bv. 15 min" value="<?= htmlspecialchars($onderdeel['tijdIndicatie']) ?>"></td>
                            <td><textarea name="onderdelen[<?= $i ?>][doel]" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($onderdeel['doel']) ?></textarea></td>
                            <td><textarea name="onderdelen[<?= $i ?>][activiteit]" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($onderdeel['activiteit']) ?></textarea></td>
                            <td>
                                <input type="text" name="onderdelen[<?= $i ?>][organisatie_en_materialen]" class="form-control form-control-sm mb-1" placeholder="bv. 2 banen, pop per groepje" value="<?= htmlspecialchars($onderdeel['organisatieEnMaterialen']) ?>">
                                <select name="onderdelen[<?= $i ?>][materiaal_ids][]" class="form-select form-select-sm" multiple size="3">
                                    <?php foreach ($materialen as $materiaal): ?>
                                        <option value="<?= $materiaal->id ?>" <?= in_array($materiaal->id, $onderdeel['materiaalIds'], true) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($materiaal->naam) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><textarea name="onderdelen[<?= $i ?>][didactische_aanwijzingen]" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($onderdeel['didactischeAanwijzingen']) ?></textarea></td>
                            <td>
                                <?php if ($i >= 4): ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger onderdeel-verwijderen"><i class="bi bi-trash"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Opslaan</button>
        <a href="/lesplanningen" class="btn btn-outline-secondary">Annuleren</a>
    </div>
</form>
