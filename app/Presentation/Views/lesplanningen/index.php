<?php
/** @var \App\BLL\Models\Lesplanning[] $lesplanningen */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Lesplanningen</h1>
    <a href="/lesplanningen/nieuw" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Nieuwe lesplanning</a>
</div>

<table class="table table-striped align-middle">
    <thead>
    <tr><th>Datum</th><th>Groep</th><th>Tijd</th><th>Locatie</th><th>Instructeur</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($lesplanningen as $lesplanning): ?>
        <tr>
            <td><?= $lesplanning->datum->format('d-m-Y') ?></td>
            <td><?= htmlspecialchars($lesplanning->groep->naam) ?></td>
            <td><?= htmlspecialchars($lesplanning->beginTijd) ?> &ndash; <?= htmlspecialchars($lesplanning->eindTijd) ?></td>
            <td><?= htmlspecialchars($lesplanning->locatie) ?></td>
            <td><?= htmlspecialchars($lesplanning->instructeur->naam) ?></td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-secondary" disabled><i class="bi bi-printer"></i> Print (later)</button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
