<?php
/** @var \App\BLL\Models\Materiaal[] $materialen */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Materiaal</h1>
    <a href="/materiaal/nieuw" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Materiaal toevoegen</a>
</div>
<p class="text-muted small">Centraal beheerd materiaalregister &mdash; door iedere instructeur te beheren. Elke wijziging wordt gelogd met AUTHID.</p>

<table class="table table-striped align-middle">
    <thead>
    <tr><th>Naam</th><th>Categorie</th><th>Status</th><th class="text-end">Acties</th></tr>
    </thead>
    <tbody>
    <?php foreach ($materialen as $materiaal): ?>
        <tr>
            <td><?= htmlspecialchars($materiaal->naam) ?></td>
            <td><?= $materiaal->categorie !== null ? htmlspecialchars($materiaal->categorie) : '<span class="text-muted">&ndash;</span>' ?></td>
            <td><span class="badge text-bg-<?= $materiaal->actief ? 'success' : 'secondary' ?>"><?= $materiaal->actief ? 'Actief' : 'Inactief' ?></span></td>
            <td class="text-end">
                <div class="btn-group btn-group-sm">
                    <a href="/materiaal/<?= $materiaal->id ?>/bewerken" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                    <form method="post" action="/materiaal/<?= $materiaal->id ?>/activeren" class="d-inline">
                        <button type="submit" class="btn btn-outline-secondary" title="<?= $materiaal->actief ? 'Deactiveren' : 'Activeren' ?>">
                            <i class="bi bi-<?= $materiaal->actief ? 'pause' : 'play' ?>"></i>
                        </button>
                    </form>
                    <form method="post" action="/materiaal/<?= $materiaal->id ?>/verwijderen" class="d-inline"
                          onsubmit="return confirm('Materiaal \'<?= htmlspecialchars($materiaal->naam, ENT_QUOTES) ?>\' definitief verwijderen?');">
                        <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (empty($materialen)): ?>
        <tr><td colspan="4" class="text-center text-muted">Nog geen materiaal aangemaakt.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
