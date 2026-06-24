<?php
/** @var \App\BLL\Models\Lesplanning[] $lesplanningen */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Lesplanningen</h1>
    <a href="/lesplanningen/nieuw" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Nieuwe lesplanning</a>
</div>

<table class="table table-striped align-middle">
    <thead>
    <tr><th>Datum</th><th>Groep</th><th>Tijd</th><th>Locatie</th><th>Instructeur</th><th class="text-end">Acties</th></tr>
    </thead>
    <tbody>
    <?php foreach ($lesplanningen as $lesplanning): ?>
        <tr>
            <td><?= $lesplanning->datum->format('d-m-Y') ?></td>
            <td><?= htmlspecialchars($lesplanning->groep->naam) ?></td>
            <td><?= htmlspecialchars($lesplanning->beginTijd) ?> &ndash; <?= htmlspecialchars($lesplanning->eindTijd) ?></td>
            <td><?= $lesplanning->locatie !== '' ? htmlspecialchars($lesplanning->locatie) : '<span class="text-muted">&ndash;</span>' ?></td>
            <td><?= htmlspecialchars($lesplanning->instructeur->naam) ?></td>
            <td class="text-end">
                <div class="btn-group btn-group-sm">
                    <a href="/lesplanningen/<?= $lesplanning->id ?>/bewerken" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                    <button class="btn btn-outline-secondary" disabled><i class="bi bi-printer"></i></button>
                    <form method="post" action="/lesplanningen/<?= $lesplanning->id ?>/verwijderen" class="d-inline"
                          onsubmit="return confirm('Lesplanning van <?= $lesplanning->datum->format('d-m-Y') ?> verwijderen?');">
                        <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (empty($lesplanningen)): ?>
        <tr><td colspan="6" class="text-center text-muted">Nog geen lesplanningen aangemaakt.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
