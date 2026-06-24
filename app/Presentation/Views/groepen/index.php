<?php
/** @var \App\BLL\Models\Groep[] $groepen */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Groepen</h1>
    <button class="btn btn-primary btn-sm" disabled><i class="bi bi-plus-lg"></i> Nieuwe groep (later)</button>
</div>

<table class="table table-striped align-middle">
    <thead>
    <tr>
        <th>Naam</th>
        <th>Afdeling</th>
        <th>Instructeur(s)</th>
        <th>Aantal leden</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($groepen as $groep): ?>
        <tr>
            <td><?= htmlspecialchars($groep->naam) ?></td>
            <td><?= htmlspecialchars($groep->afdeling->naam) ?></td>
            <td><?= htmlspecialchars(implode(', ', array_map(static fn ($i) => $i->naam, $groep->instructeurs))) ?></td>
            <td><?= $groep->aantalLeden ?></td>
            <td class="text-end">
                <a href="/lesplanningen/nieuw" class="btn btn-sm btn-outline-secondary">Lesplanning maken</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
