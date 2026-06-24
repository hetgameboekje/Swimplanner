<?php
/** @var \App\BLL\Models\Groep $groep */
/** @var \App\BLL\Models\Lid[] $leden */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0"><?= htmlspecialchars($groep->naam) ?></h1>
        <p class="text-muted small mb-0">
            <?= htmlspecialchars($groep->afdeling->naam) ?> &middot;
            <?= $groep->startDatum->format('d-m-Y') ?> &ndash; <?= $groep->eindDatum?->format('d-m-Y') ?? 'heden' ?>
        </p>
    </div>
    <a href="/groepen/<?= $groep->id ?>/bewerken" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i> Groep bewerken</a>
</div>

<div class="card mb-3">
    <div class="card-header">Leden (<?= count($leden) ?>)</div>
    <div class="card-body p-0">
        <table class="table mb-0 align-middle">
            <thead>
            <tr>
                <th>Voornaam</th>
                <th>Achternaam</th>
                <th>Geboortejaar</th>
                <th>Contactgegevens</th>
                <th class="text-end">Acties</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($leden as $lid): ?>
                <tr>
                    <td><?= htmlspecialchars($lid->voornaam) ?></td>
                    <td><?= htmlspecialchars($lid->achternaam) ?></td>
                    <td><?= $lid->geboortejaar ?? '<span class="text-muted">&ndash;</span>' ?></td>
                    <td><?= $lid->contactgegevens !== null ? htmlspecialchars($lid->contactgegevens) : '<span class="text-muted">&ndash;</span>' ?></td>
                    <td class="text-end">
                        <form method="post" action="/groepen/<?= $groep->id ?>/leden/<?= $lid->id ?>/uitschrijven"
                              onsubmit="return confirm('<?= htmlspecialchars($lid->volledigeNaam(), ENT_QUOTES) ?> uitschrijven bij deze groep?');">
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-person-dash"></i> Uitschrijven</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($leden)): ?>
                <tr><td colspan="5" class="text-center text-muted">Nog geen leden in deze groep.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">Lid toevoegen</div>
    <div class="card-body">
        <form method="post" action="/groepen/<?= $groep->id ?>/leden" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Voornaam</label>
                <input type="text" name="voornaam" class="form-control" required maxlength="100">
            </div>
            <div class="col-md-3">
                <label class="form-label">Achternaam</label>
                <input type="text" name="achternaam" class="form-control" required maxlength="100">
            </div>
            <div class="col-md-2">
                <label class="form-label">Jaartal</label>
                <input type="number" name="geboortejaar" class="form-control" placeholder="bv. 2014" min="1900" max="<?= date('Y') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Contactgegevens</label>
                <input type="text" name="contactgegevens" class="form-control" placeholder="telefoon/e-mail (optioneel)">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary"><i class="bi bi-person-plus"></i> Lid toevoegen</button>
            </div>
        </form>
    </div>
</div>
