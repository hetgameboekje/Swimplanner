<?php
/** @var \App\BLL\Models\Les|null $les */
/** @var \App\BLL\Models\Groep[] $groepen */
/** @var \App\BLL\Models\Gebruiker[] $instructeurs */
/** @var \App\BLL\Models\LesType[] $lesTypes */
/** @var string $actiePad */
/** @var int[] $geselecteerdeGroepIds */
/** @var int[] $geselecteerdeInstructeurIds */

$standaardDatum = $les?->datum->format('Y-m-d') ?? (new DateTimeImmutable())->format('Y-m-d');
?>
<h1 class="h4 mb-3"><?= $les === null ? 'Nieuwe les' : 'Les bewerken' ?></h1>

<form method="post" action="<?= htmlspecialchars($actiePad) ?>">
    <div class="card mb-3">
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label class="form-label">Groep(en)</label>
                <select name="groep_ids[]" class="form-select" multiple size="5" required>
                    <?php foreach ($groepen as $groep): ?>
                        <option value="<?= $groep->id ?>" <?= in_array($groep->id, $geselecteerdeGroepIds, true) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($groep->naam) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Meerdere groepen mogelijk (bv. 2 groepen die samen 1 uur van een lesblok draaien). Ctrl ingedrukt houden om meerdere te selecteren.</div>
                <?php if (empty($groepen)): ?>
                    <div class="form-text text-danger">Er zijn nog geen groepen — maak er eerst één aan.</div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Instructeur(s)</label>
                <select name="instructeur_ids[]" class="form-select" multiple size="5" required>
                    <?php foreach ($instructeurs as $instructeur): ?>
                        <option value="<?= $instructeur->id ?>" <?= in_array($instructeur->id, $geselecteerdeInstructeurIds, true) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($instructeur->naam) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Meerdere instructeurs mogelijk.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Datum</label>
                <input type="date" name="datum" class="form-control" required value="<?= $standaardDatum ?>">
                <div class="form-text">Elke datum is toegestaan (ook incidentele lessen, bv. voor aspiranten of kader).</div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select" required>
                    <?php foreach ($lesTypes as $type): ?>
                        <option value="<?= $type->value ?>" <?= $les?->type === $type ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst($type->value)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Begintijd</label>
                <input type="time" name="begin_tijd" class="form-control" value="<?= htmlspecialchars(substr($les?->beginTijd ?? '', 0, 5)) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Eindtijd</label>
                <input type="time" name="eind_tijd" class="form-control" value="<?= htmlspecialchars(substr($les?->eindTijd ?? '', 0, 5)) ?>">
                <div class="form-text">Optioneel — handig bij meerdere lessen op één dag.</div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Opslaan</button>
        <a href="/lessen" class="btn btn-outline-secondary">Annuleren</a>
    </div>
</form>
