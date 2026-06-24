<?php
/** @var \App\BLL\Models\Groep[] $groepen */
/** @var string[] $waarschuwingen */
?>
<h1 class="h4 mb-3">Dashboard</h1>

<?php if (!empty($waarschuwingen)): ?>
    <div class="alert alert-info">
        <strong><i class="bi bi-info-circle"></i> Informatieve waarschuwingen</strong>
        <ul class="mb-0 mt-2">
            <?php foreach ($waarschuwingen as $waarschuwing): ?>
                <li><?= htmlspecialchars($waarschuwing) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php else: ?>
    <div class="alert alert-success"><i class="bi bi-check-circle"></i> Geen openstaande waarschuwingen.</div>
<?php endif; ?>

<div class="row">
    <?php foreach ($groepen as $groep): ?>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6 card-title"><?= htmlspecialchars($groep->naam) ?></h2>
                    <p class="card-text text-muted small mb-1"><?= htmlspecialchars($groep->afdeling->naam) ?></p>
                    <p class="card-text"><?= $groep->aantalLeden ?> leden</p>
                    <a href="/groepen" class="btn btn-sm btn-outline-primary">Bekijk groep</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
