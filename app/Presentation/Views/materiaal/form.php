<?php
/** @var \App\BLL\Models\Materiaal|null $materiaal */
/** @var string $actiePad */
?>
<h1 class="h4 mb-3"><?= $materiaal === null ? 'Materiaal toevoegen' : 'Materiaal bewerken' ?></h1>

<form method="post" action="<?= htmlspecialchars($actiePad) ?>">
    <div class="card mb-3">
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label class="form-label">Naam</label>
                <input type="text" name="naam" class="form-control" required maxlength="150"
                       value="<?= htmlspecialchars($materiaal?->naam ?? '') ?>" placeholder="bv. Reddingsvest">
            </div>
            <div class="col-md-6">
                <label class="form-label">Categorie</label>
                <input type="text" name="categorie" class="form-control" maxlength="100"
                       value="<?= htmlspecialchars($materiaal?->categorie ?? '') ?>" placeholder="bv. Reddingsmateriaal (optioneel)">
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Opslaan</button>
        <a href="/materiaal" class="btn btn-outline-secondary">Annuleren</a>
    </div>
</form>
