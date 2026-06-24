<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instructeursportaal &mdash; Reddingsbrigade</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-3 topbar d-flex justify-content-between">
    <span class="navbar-brand mb-0 h1">
        <i class="bi bi-life-preserver"></i> Instructeursportaal &mdash; Zwemmend redden
    </span>
    <span class="navbar-text text-white-50 small d-flex align-items-center gap-2">
        Ingelogd als <strong class="text-white"><?= htmlspecialchars(\App\Core\HuidigeGebruiker::naam()) ?></strong>
        (<?= htmlspecialchars(\App\Core\HuidigeGebruiker::rol()?->value ?? '') ?>)
        <form method="post" action="/uitloggen" class="d-inline">
            <button type="submit" class="btn btn-sm btn-outline-light">Uitloggen</button>
        </form>
    </span>
</nav>
<div class="d-flex app-shell">
