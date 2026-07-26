<?php
/**
 * Landing Page
 *
 * Premium dark cinematic landing page for AuraPay.
 */

declare(strict_types=1);

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/product.php';

$p = product();
$landing = $p['landing'];

$iconMap = [
    'shield-check' => 'bi-shield-check',
    'zap' => 'bi-lightning-charge',
    'lock' => 'bi-lock',
    'wallet' => 'bi-wallet2',
    'user-check' => 'bi-person-check',
    'headset' => 'bi-headset',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($p['name']) ?> — <?= htmlspecialchars($p['tagline']) ?></title>
    <meta name="description" content="<?= htmlspecialchars($p['description']) ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= BASE_PATH ?>/assets/css/aurapay.css" rel="stylesheet">
</head>
<body class="landing-body bg-grain">

<!-- Nav -->
<nav class="landing-nav">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between py-3">
            <a href="<?= BASE_PATH ?>/index.php" class="landing-brand">
                <span class="brand-icon"><i class="bi bi-wallet2"></i></span>
                <span><?= htmlspecialchars($p['name']) ?></span>
            </a>
            <div class="d-none d-md-flex align-items-center gap-4">
                <a href="#features" class="nav-link-inline">Features</a>
                <a href="#how" class="nav-link-inline">How it works</a>
                <a href="#faq" class="nav-link-inline">FAQ</a>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= BASE_PATH ?>/auth/login.php" class="btn btn-ghost-gold btn-sm">Sign in</a>
                <a href="<?= BASE_PATH ?>/auth/register.php" class="btn btn-gold btn-sm">Get started</a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero -->
<section class="hero-section">
    <div class="hero-glow-1"></div>
    <div class="hero-glow-2"></div>
    <div class="container position-relative">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="hero-badge"><i class="bi bi-shield-check me-1"></i>Bank-grade security · BSP-registered</span>
                <h1 class="hero-title">Borrow with<br><span class="gold-text">quiet confidence.</span></h1>
                <p class="hero-subtitle"><?= htmlspecialchars($landing['hero_subtitle']) ?></p>
                <div class="hero-cta">
                    <a href="<?= BASE_PATH ?>/auth/register.php" class="btn btn-gold btn-lg"><?= htmlspecialchars($landing['hero_cta_primary']) ?> <i class="bi bi-arrow-right ms-1"></i></a>
                    <a href="#how" class="btn btn-ghost-gold btn-lg"><?= htmlspecialchars($landing['hero_cta_secondary']) ?></a>
                </div>
                <div class="hero-trust">
                    <i class="bi bi-lock-fill" style="color: var(--gold-dim);"></i>
                    <span>Your data is encrypted and never sold.</span>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-card">
                    <div class="hero-card-glow"></div>
                    <div class="position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em;">Active Loan</span>
                            <span class="badge badge-emerald">Approved</span>
                        </div>
                        <div class="text-muted mb-1" style="font-size: 0.75rem;">Outstanding balance</div>
                        <div class="hero-amount">₱25,000</div>
                        <div class="hero-terms">
                            <div><span class="term-label">Term</span><span class="term-value">6 months</span></div>
                            <div><span class="term-label">Monthly</span><span class="term-value">₱5,067</span></div>
                            <div><span class="term-label">Rate</span><span class="term-value">3.5%/mo</span></div>
                        </div>
                        <div class="d-flex justify-content-between mb-2" style="font-size: 0.875rem;"><span class="text-muted">Repaid</span><span>₱20,267 / ₱30,400</span></div>
                        <div class="progress" style="height: 6px;"><div class="progress-bar" style="width: 67%"></div></div>
                        <div class="text-end mt-2" style="font-size: 0.75rem; color: var(--gold-bright);">67% complete</div>
                    </div>
                </div>
                <div class="hero-card-float d-none d-sm-flex align-items-center gap-2">
                    <span class="auth-feature-icon" style="background: rgba(52,211,153,0.1); border-color: rgba(52,211,153,0.2); color: var(--emerald);"><i class="bi bi-graph-up-arrow"></i></span>
                    <div>
                        <div style="font-size: 0.7rem; color: var(--ink-3);">Credit Score</div>
                        <div style="font-family: var(--font-display); font-weight: 500; color: var(--ink);">742 <span style="font-size: 0.75rem; color: var(--emerald);">Very Good</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- rest of page unchanged -->

<!-- Footer -->
<footer class="landing-footer">
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-lg-4">
                <a href="<?= BASE_PATH ?>/index.php" class="landing-brand mb-3 d-inline-flex">
                    <span class="brand-icon"><i class="bi bi-wallet2"></i></span>
                    <span><?= htmlspecialchars($p['name']) ?></span>
                </a>
                <p class="small" style="max-width: 20rem;"><?= htmlspecialchars($p['description']) ?></p>
            </div>
            <div class="col-lg-2 col-6">
                <h6 class="footer-heading">Product</h6>
                <a href="#features" class="footer-link">Features</a>
                <a href="#how" class="footer-link">How it works</a>
                <a href="<?= BASE_PATH ?>/auth/register.php" class="footer-link">Get started</a>
            </div>
            <div class="col-lg-2 col-6">
                <h6 class="footer-heading">Company</h6>
                <a href="#" class="footer-link">About us</a>
                <a href="#" class="footer-link">Careers</a>
                <a href="#" class="footer-link">Contact</a>
            </div>
            <div class="col-lg-4">
                <h6 class="footer-heading">Contact</h6>
                <p class="footer-text"><i class="bi bi-envelope me-2"></i><?= htmlspecialchars($p['support_email']) ?></p>
                <p class="footer-text"><i class="bi bi-telephone me-2"></i><?= htmlspecialchars($p['support_phone']) ?></p>
                <p class="footer-text"><i class="bi bi-geo-alt me-2"></i><?= htmlspecialchars($p['address']) ?></p>
            </div>
        </div>
        <hr class="footer-divider">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <span class="small">&copy; <?= date('Y') ?> <?= htmlspecialchars($p['company']) ?>. All rights reserved.</span>
            <div class="d-flex gap-3">
                <?php foreach ($p['social'] as $name => $url): ?>
                <a href="<?= htmlspecialchars($url) ?>" class="footer-social"><i class="bi bi-<?= $name ?>"></i></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_PATH ?>/assets/js/app.js"></script>
</body>
</html>
