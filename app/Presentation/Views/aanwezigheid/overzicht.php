<?php
/**
 * @var array<int, array{groep: \App\BLL\Models\Groep, lessen: \App\BLL\Models\Les[], leden: \App\BLL\Models\Lid[], matrix: array<int, array<int, ?\App\BLL\Models\AanwezigheidStatus>>}> $overzicht
 */
?>
<h1 class="h4 mb-3">Aanwezigheidsoverzicht</h1>
<p class="text-muted small">Alle leden, per groep, met hun aanwezigheid per lesdatum. Klik op een datum-kolom niet aanpasbaar &mdash; registreren/wijzigen gaat via de les zelf (klembord-icoon op de Lessen-pagina).</p>

<?php if (empty($overzicht)): ?>
    <div class="alert alert-info">Geen groepen om te tonen.</div>
<?php endif; ?>

<?php foreach ($overzicht as $sectie): ?>
    <?php
    $groep = $sectie['groep'];
    $lessen = $sectie['lessen'];
    $leden = $sectie['leden'];
    $matrix = $sectie['matrix'];
    ?>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><strong><?= htmlspecialchars($groep->naam) ?></strong> <span class="text-muted small"><?= htmlspecialchars($groep->afdeling->naam) ?></span></span>
            <a href="/groepen/<?= $groep->id ?>" class="btn btn-sm btn-outline-secondary">Groep bekijken</a>
        </div>
        <div class="card-body p-0">
            <?php if (empty($leden)): ?>
                <p class="text-muted p-3 mb-0">Nog geen leden in deze groep.</p>
            <?php elseif (empty($lessen)): ?>
                <p class="text-muted p-3 mb-0">Nog geen lessen voor deze groep.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0 align-middle">
                        <thead class="table-light">
                        <tr>
                            <th>Lid</th>
                            <?php foreach ($lessen as $les): ?>
                                <th class="text-center" title="<?= $les->datum->format('d-m-Y') ?>">
                                    <?= $les->datum->format('d-m') ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($leden as $lid): ?>
                            <tr>
                                <td><?= htmlspecialchars($lid->volledigeNaam()) ?></td>
                                <?php foreach ($lessen as $les): ?>
                                    <?php $status = $matrix[$lid->id][$les->id] ?? null; ?>
                                    <td class="text-center">
                                        <?php if ($status !== null): ?>
                                            <span class="badge text-bg-<?= $status->badgeKleur() ?>" title="<?= htmlspecialchars($status->label()) ?>">
                                                <?= htmlspecialchars($status->kort()) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">&ndash;</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>

<div class="small text-muted">
    Legenda:
    <span class="badge text-bg-success">A</span> Aanwezig &middot;
    <span class="badge text-bg-danger">Af</span> Afwezig &middot;
    <span class="badge text-bg-warning">TL</span> Te laat &middot;
    <span class="badge text-bg-secondary">Agm</span> Afgemeld &middot;
    <span class="text-muted">&ndash;</span> nog niet geregistreerd
</div>
