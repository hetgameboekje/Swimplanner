<?php
/** @var \App\BLL\Models\Materiaal[] $materialen */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Materiaal</h1>
    <button class="btn btn-primary btn-sm" disabled><i class="bi bi-plus-lg"></i> Materiaal toevoegen (later)</button>
</div>
<p class="text-muted small">Centraal beheerd materiaalregister &mdash; door iedere instructeur te beheren. Elke wijziging wordt later gelogd met AUTHID.</p>

<table class="table table-striped align-middle">
    <thead>
    <tr><th>Naam</th><th>Categorie</th><th>Status</th></tr>
    </thead>
    <tbody>
    <?php foreach ($materialen as $materiaal): ?>
        <tr>
            <td><?= htmlspecialchars($materiaal->naam) ?></td>
            <td><?= htmlspecialchars($materiaal->categorie) ?></td>
            <td><span class="badge text-bg-<?= $materiaal->actief ? 'success' : 'secondary' ?>"><?= $materiaal->actief ? 'Actief' : 'Inactief' ?></span></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
