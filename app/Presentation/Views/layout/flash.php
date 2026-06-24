<?php
/** @var array{type: string, bericht: string}|null $flash */
?>
<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($flash['bericht']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Sluiten"></button>
    </div>
<?php endif; ?>
