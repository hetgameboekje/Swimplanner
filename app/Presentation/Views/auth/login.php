<?php
/** @var array{type: string, bericht: string}|null $flash */
?>
<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inloggen &mdash; Instructeursportaal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card shadow-sm" style="width: 360px;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <i class="bi bi-life-preserver fs-1 text-primary"></i>
                <h1 class="h5 mt-2 mb-0">Instructeursportaal</h1>
                <small class="text-muted">Reddingsbrigade &mdash; zwemmend redden</small>
            </div>

            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['bericht']) ?></div>
            <?php endif; ?>

            <form method="post" action="/login">
                <div class="mb-3">
                    <label class="form-label">E-mailadres</label>
                    <input type="email" name="email" class="form-control" placeholder="naam@reddingsbrigade.nl" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Wachtwoord</label>
                    <input type="password" name="wachtwoord" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Inloggen</button>
            </form>
            <p class="text-muted small mt-3 mb-0">Demo-accounts (wachtwoord <code>demo123</code>): timo@reddingsbrigade.nl, anna@reddingsbrigade.nl, sven@reddingsbrigade.nl</p>
        </div>
    </div>
</div>
</body>
</html>
