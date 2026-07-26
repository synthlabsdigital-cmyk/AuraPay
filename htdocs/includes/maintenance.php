<?php
/**
 * Maintenance Page
 */

declare(strict_types=1);
$p = product();
$msg = $msg ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance &middot; <?= htmlspecialchars($p['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= BASE_PATH ?>/assets/css/aurapay.css" rel="stylesheet">
</head>
<body class="auth-body bg-grain">
<div class="auth-split">
    <div class="auth-form-panel">
        <div class="auth-wrapper text-center">
            <div class="d-flex align-items-center justify-content-center gap-3 mb-5">
                <span class="auth-brand-icon"><i class="bi bi-wallet2"></i></span>
                <span class="auth-brand-text"><?= htmlspecialchars($p['name']) ?></span>
            </div>
            <div class="card-elevated p-5">
                <i class="bi bi-tools" style="font-size:3.5rem;color:var(--gold-bright)"></i>
                <h2 class="mt-4 mb-2" style="font-family: var(--font-display); font-weight: 500;"><?= htmlspecialchars($msg['title'] ?? 'Under Maintenance') ?></h2>
                <p class="text-muted"><?= htmlspecialchars($msg['message'] ?? 'We are performing scheduled maintenance. Please check back shortly.') ?></p>
                <?php if (!empty($msg['end_at'])): ?>
                    <p class="small text-muted mt-3">Expected completion: <?= Util::formatDateTime($msg['end_at']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
