<?php
/** @var \App\BLL\Models\Les $les */
/** @var \App\BLL\Models\AanwezigheidRegel[] $regels */

use App\BLL\Models\AanwezigheidStatus;
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Aanwezigheid</h1>
        <p class="text-muted small mb-0">
            <?= $les->datum->format('d-m-Y') ?>
            &middot; <?= htmlspecialchars(implode(', ', array_map(static fn ($g) => $g->naam, $les->groepen))) ?>
        </p>
    </div>
    <a href="/lessen" class="btn btn-outline-secondary btn-sm">&larr; Terug naar lessen</a>
</div>

<?php if (empty($regels)): ?>
    <div class="alert alert-warning">
        Geen leden gevonden voor de groep(en) van deze les. Voeg eerst leden toe via de groeppagina.
    </div>
<?php else: ?>
    <form method="post" action="/lessen/<?= $les->id ?>/aanwezigheid">
        <table class="table table-striped align-middle">
            <thead>
            <tr>
                <th>Naam</th>
                <th style="width:20%">Status</th>
                <th style="width:35%">Opmerking</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($regels as $regel): ?>
                <tr>
                    <td><?= htmlspecialchars($regel->lid->volledigeNaam()) ?></td>
                    <td>
                        <select name="status[<?= $regel->lid->id ?>]" class="form-select form-select-sm" required>
                            <?php foreach (AanwezigheidStatus::cases() as $status): ?>
                                <option value="<?= $status->value ?>"
                                    <?= ($regel->status ?? AanwezigheidStatus::Aanwezig) === $status ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($status->label()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="opmerking[<?= $regel->lid->id ?>]" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($regel->opmerking ?? '') ?>" placeholder="optioneel">
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Aanwezigheid opslaan</button>
    </form>
<?php endif; ?>
