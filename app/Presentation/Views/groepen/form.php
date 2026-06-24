<?php
/** @var \App\BLL\Models\Groep|null $groep */
/** @var \App\BLL\Models\Afdeling[] $afdelingen */
/** @var \App\BLL\Models\Gebruiker[] $instructeurs */
/** @var int[] $geselecteerdeInstructeurIds */
/** @var string $actiePad */
/** @var string $vandaag */
?>
<h1 class="h4 mb-3"><?= $groep === null ? 'Nieuwe groep' : 'Groep bewerken' ?></h1>

<form method="post" action="<?= htmlspecialchars($actiePad) ?>">
    <div class="card mb-3">
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label class="form-label">Naam</label>
                <input type="text" name="naam" class="form-control" required maxlength="150"
                       value="<?= htmlspecialchars($groep?->naam ?? '') ?>" placeholder="bv. Zwemmend Redder 2">
            </div>
            <div class="col-md-6">
                <label class="form-label">Afdeling</label>
                <select name="afdeling_id" class="form-select" required>
                    <?php foreach ($afdelingen as $afdeling): ?>
                        <option value="<?= $afdeling->id ?>" <?= $groep?->afdeling->id === $afdeling->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($afdeling->naam) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Startdatum</label>
                <input type="date" name="start_datum" class="form-control" required
                       value="<?= htmlspecialchars($groep?->startDatum->format('Y-m-d') ?? $vandaag) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Einddatum</label>
                <input type="date" name="eind_datum" class="form-control"
                       value="<?= htmlspecialchars($groep?->eindDatum?->format('Y-m-d') ?? '') ?>">
                <div class="form-text">Leeg laten als de groep nog actief doorloopt.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Instructeur(s)</label>
                <select name="instructeur_ids[]" class="form-select" multiple size="5">
                    <?php foreach ($instructeurs as $instructeur): ?>
                        <option value="<?= $instructeur->id ?>" <?= in_array($instructeur->id, $geselecteerdeInstructeurIds, true) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($instructeur->naam) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Houd Ctrl (Windows) ingedrukt om meerdere instructeurs te selecteren.</div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Opslaan</button>
        <a href="/groepen" class="btn btn-outline-secondary">Annuleren</a>
    </div>
</form>
