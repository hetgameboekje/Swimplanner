<?php $huidigePad = $_SERVER['REQUEST_URI'] ?? ''; ?>
<aside class="sidebar bg-light border-end">
    <div class="list-group list-group-flush">
        <a href="/" class="list-group-item list-group-item-action <?= $huidigePad === '/' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="/groepen" class="list-group-item list-group-item-action <?= str_starts_with($huidigePad, '/groepen') ? 'active' : '' ?>">
            <i class="bi bi-people"></i> Groepen
        </a>
        <a href="/lessen" class="list-group-item list-group-item-action <?= str_starts_with($huidigePad, '/lessen') ? 'active' : '' ?>">
            <i class="bi bi-calendar3"></i> Lessen
        </a>
        <a href="/lesplanningen" class="list-group-item list-group-item-action <?= str_starts_with($huidigePad, '/lesplanningen') ? 'active' : '' ?>">
            <i class="bi bi-journal-text"></i> Lesplanningen
        </a>
        <a href="/aanwezigheid" class="list-group-item list-group-item-action <?= str_starts_with($huidigePad, '/aanwezigheid') ? 'active' : '' ?>" title="Overzicht per groep en lesdatum &mdash; registreren gaat via een les">
            <i class="bi bi-clipboard-check"></i> Aanwezigheid
        </a>
        <a href="/materiaal" class="list-group-item list-group-item-action <?= str_starts_with($huidigePad, '/materiaal') ? 'active' : '' ?>">
            <i class="bi bi-box-seam"></i> Materiaal
        </a>
        <hr>
        <a href="#" class="list-group-item list-group-item-action disabled">
            <i class="bi bi-graph-up"></i> Jaarplanning <span class="badge text-bg-secondary float-end">later</span>
        </a>
        <a href="#" class="list-group-item list-group-item-action disabled">
            <i class="bi bi-water"></i> Varend redden (AVR) <span class="badge text-bg-secondary float-end">won&#39;t-have</span>
        </a>
    </div>
</aside>
