<?php
/** @var \App\BLL\Models\Les[] $lessen */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Lessen</h1>
    <a href="/lessen/nieuw" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Nieuwe les</a>
</div>

<table class="table table-striped align-middle">
    <thead>
    <tr>
        <th>Datum</th>
        <th>Tijd</th>
        <th>Groep(en)</th>
        <th>Type</th>
        <th>Instructeur(s)</th>
        <th>Lesplanning</th>
        <th class="text-end">Acties</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($lessen as $les): ?>
        <tr>
            <td><?= $les->datum->format('d-m-Y') ?> <span class="text-muted small">(<?= $les->datum->format('D') ?>)</span></td>
            <td>
                <?php if ($les->beginTijd !== null): ?>
                    <?= htmlspecialchars(substr($les->beginTijd, 0, 5)) ?><?= $les->eindTijd !== null ? '–' . htmlspecialchars(substr($les->eindTijd, 0, 5)) : '' ?>
                <?php else: ?>
                    <span class="text-muted">&ndash;</span>
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars(implode(', ', array_map(static fn ($g) => $g->naam, $les->groepen))) ?></td>
            <td><span class="badge text-bg-light border"><?= htmlspecialchars($les->type->value) ?></span></td>
            <td><?= htmlspecialchars(implode(', ', array_map(static fn ($i) => $i->naam, $les->instructeurs))) ?></td>
            <td>
                <?php if ($les->heeftLesplanning): ?>
                    <span class="badge text-bg-success">Aanwezig</span>
                <?php else: ?>
                    <span class="badge text-bg-warning text-dark">Ontbreekt</span>
                <?php endif; ?>
            </td>
            <td class="text-end">
                <div class="btn-group btn-group-sm">
                    <a href="/lessen/<?= $les->id ?>/bewerken" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                    <form method="post" action="/lessen/<?= $les->id ?>/verwijderen" class="d-inline"
                          onsubmit="return confirm('Les op <?= $les->datum->format('d-m-Y') ?> verwijderen?');">
                        <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (empty($lessen)): ?>
        <tr><td colspan="7" class="text-center text-muted">Nog geen lessen aangemaakt.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
