<?php
/** @var \App\BLL\Models\Groep[] $groepen */
/** @var \App\BLL\Models\Gebruiker[] $instructeurs */
/** @var \App\BLL\Models\LesType[] $lesTypes */
/** @var string $vandaag */
?>
<h1 class="h4 mb-3">Lessen in bulk inplannen</h1>

<div class="alert alert-info">
    Vul de lesgegevens één keer in. Er worden dan meerdere lessen aangemaakt
    met dezelfde groepen, instructeurs, tijden en locatie — handig om in één
    keer een seizoen of jaar aan reguliere lessen klaar te zetten (max. 52).
</div>

<form method="post" action="/lessen/bulk">
    <div class="card mb-3">
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label class="form-label">Groep(en)</label>
                <select name="groep_ids[]" class="form-select" multiple size="5" required>
                    <?php foreach ($groepen as $groep): ?>
                        <option value="<?= $groep->id ?>"><?= htmlspecialchars($groep->naam) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($groepen)): ?>
                    <div class="form-text text-danger">Er zijn nog geen groepen — maak er eerst één aan.</div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Instructeur(s)</label>
                <select name="instructeur_ids[]" class="form-select" multiple size="5" required>
                    <?php foreach ($instructeurs as $instructeur): ?>
                        <option value="<?= $instructeur->id ?>"><?= htmlspecialchars($instructeur->naam) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Startdatum (1e les)</label>
                <input type="date" name="start_datum" class="form-control" required value="<?= $vandaag ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Interval (dagen)</label>
                <input type="number" name="interval_dagen" class="form-control" min="1" value="7" required>
                <div class="form-text">7 = wekelijks.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Aantal lessen</label>
                <input type="number" name="aantal_lessen" class="form-control" min="1" max="52" value="52" required>
                <div class="form-text">Maximaal 52.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select" required>
                    <?php foreach ($lesTypes as $type): ?>
                        <option value="<?= $type->value ?>"><?= htmlspecialchars(ucfirst($type->value)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Begintijd</label>
                <input type="time" name="begin_tijd" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Eindtijd</label>
                <input type="time" name="eind_tijd" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Locatie</label>
                <input type="text" name="locatie" class="form-control" placeholder="bv. Zwembad De Roerdomp, baan 1">
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Lessen aanmaken</button>
        <a href="/lessen" class="btn btn-outline-secondary">Annuleren</a>
    </div>
</form>
