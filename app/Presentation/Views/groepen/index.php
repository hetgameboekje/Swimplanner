<?php
/** @var \App\BLL\Models\Groep[] $groepen */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Groepen</h1>
    <a href="/groepen/nieuw" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Nieuwe groep</a>
</div>

<table class="table table-striped align-middle">
    <thead>
    <tr>
        <th>Naam</th>
        <th>Afdeling</th>
        <th>Periode</th>
        <th>Instructeur(s)</th>
        <th>Aantal leden</th>
        <th>Status</th>
        <th class="text-end">Acties</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($groepen as $groep): ?>
        <tr>
            <td><a href="/groepen/<?= $groep->id ?>"><?= htmlspecialchars($groep->naam) ?></a></td>
            <td><?= htmlspecialchars($groep->afdeling->naam) ?></td>
            <td>
                <?= $groep->startDatum->format('d-m-Y') ?> &ndash;
                <?= $groep->eindDatum?->format('d-m-Y') ?? '<span class="text-muted">heden</span>' ?>
            </td>
            <td>
                <?php if (empty($groep->instructeurs)): ?>
                    <span class="text-muted">geen</span>
                <?php else: ?>
                    <?= htmlspecialchars(implode(', ', array_map(static fn ($i) => $i->naam, $groep->instructeurs))) ?>
                <?php endif; ?>
            </td>
            <td><?= $groep->aantalLeden ?></td>
            <td>
                <span class="badge text-bg-<?= $groep->actief ? 'success' : 'secondary' ?>">
                    <?= $groep->actief ? 'Actief' : 'Inactief' ?>
                </span>
            </td>
            <td class="text-end">
                <div class="btn-group btn-group-sm">
                    <a href="/groepen/<?= $groep->id ?>/bewerken" class="btn btn-outline-secondary">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form method="post" action="/groepen/<?= $groep->id ?>/activeren" class="d-inline">
                        <button type="submit" class="btn btn-outline-secondary" title="<?= $groep->actief ? 'Deactiveren' : 'Activeren' ?>">
                            <i class="bi bi-<?= $groep->actief ? 'pause' : 'play' ?>"></i>
                        </button>
                    </form>
                    <form method="post" action="/groepen/<?= $groep->id ?>/verwijderen" class="d-inline"
                          onsubmit="return confirm('Groep \'<?= htmlspecialchars($groep->naam, ENT_QUOTES) ?>\' definitief verwijderen?');">
                        <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (empty($groepen)): ?>
        <tr><td colspan="7" class="text-center text-muted">Nog geen groepen aangemaakt.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
