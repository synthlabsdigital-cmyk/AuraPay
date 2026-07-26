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
    <link href="assets/css/aurapay.css" rel="stylesheet">
</head>
<body class="landing-body bg-grain">

<!-- Nav -->
<nav class="landing-nav">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between py-3">
            <a href="index.php" class="landing-brand">
                <span class="brand-icon"><i class="bi bi-wallet2"></i></span>
                <span><?= htmlspecialchars($p['name']) ?></span>
            </a>
            <div class="d-none d-md-flex align-items-center gap-4">
                <a href="#features" class="nav-link-inline">Features</a>
                <a href="#how" class="nav-link-inline">How it works</a>
                <a href="#faq" class="nav-link-inline">FAQ</a>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="auth/login.php" class="btn btn-ghost-gold btn-sm">Sign in</a>
                <a href="auth/register.php" class="btn btn-gold btn-sm">Get started</a>
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
                    <a href="auth/register.php" class="btn btn-gold btn-lg"><?= htmlspecialchars($landing['hero_cta_primary']) ?> <i class="bi bi-arrow-right ms-1"></i></a>
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

<!-- Stats -->
<section class="stats-section">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($landing['stats'] as $stat): ?>
            <div class="col-6 col-lg-3 text-center">
                <div class="stat-value-lg"><?= htmlspecialchars($stat['value']) ?></div>
                <div class="stat-label-lg"><?= htmlspecialchars($stat['label']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Features -->
<section class="features-section" id="features">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label justify-content-center">
                <span class="ornament"></span><span>Why <?= htmlspecialchars($p['name']) ?></span><span class="ornament" style="transform: rotate(180deg);"></span>
            </div>
            <h2 class="section-title">A lending partner worthy of your trust</h2>
            <p class="section-subtitle">Everything you need from a modern, dignified lending partner.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($landing['features'] as $feature): ?>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi <?= $iconMap[$feature['icon']] ?? 'bi-check-circle' ?>"></i></div>
                    <h4><?= htmlspecialchars($feature['title']) ?></h4>
                    <p><?= htmlspecialchars($feature['text']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- How it works -->
<section class="how-section" id="how">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label justify-content-center">
                <span class="ornament"></span><span>The process</span><span class="ornament" style="transform: rotate(180deg);"></span>
            </div>
            <h2 class="section-title">From application to funding</h2>
            <p class="section-subtitle">A refined five-step journey.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($landing['steps'] as $i => $step): ?>
            <div class="col-md-4 col-lg">
                <div class="step-card">
                    <div class="step-number"><?= $i + 1 ?></div>
                    <h5><?= htmlspecialchars($step['title']) ?></h5>
                    <p><?= htmlspecialchars($step['text']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="testimonials-section">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label justify-content-center">
                <span class="ornament"></span><span>Testimonials</span><span class="ornament" style="transform: rotate(180deg);"></span>
            </div>
            <h2 class="section-title">Stories from our clients</h2>
        </div>
        <div class="row g-4">
            <?php foreach ($landing['testimonials'] as $testimonial): ?>
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p>"<?= htmlspecialchars($testimonial['text']) ?>"</p>
                    <div class="testimonial-author">
                        <span class="testimonial-avatar"><?= Util::initials($testimonial['name']) ?></span>
                        <div>
                            <div style="font-weight: 500; color: var(--ink); font-size: 0.875rem;"><?= htmlspecialchars($testimonial['name']) ?></div>
                            <div style="font-size: 0.75rem; color: var(--ink-3);"><?= htmlspecialchars($testimonial['location']) ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="faq-section" id="faq">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label justify-content-center">
                <span class="ornament"></span><span>Questions</span><span class="ornament" style="transform: rotate(180deg);"></span>
            </div>
            <h2 class="section-title">Frequently asked</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <?php foreach ($landing['faq'] as $i => $faq): ?>
                    <div class="accordion-item faq-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button <?= $i>0?'collapsed':'' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>">
                                <?= htmlspecialchars($faq['q']) ?>
                            </button>
                        </h2>
                        <div id="faq<?= $i ?>" class="accordion-collapse collapse <?= $i===0?'show':'' ?>" data-bs-parent="#faqAccordion">
                            <div class="accordion-body"><?= htmlspecialchars($faq['a']) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <div class="cta-card">
            <div class="cta-glow"></div>
            <div class="position-relative">
                <div class="ornament mx-auto mb-3" style="width: 60px;"></div>
                <h2>Ready to begin?</h2>
                <p>Apply for a loan in minutes. No hidden fees, no surprises — only dignity.</p>
                <a href="auth/register.php" class="btn btn-light btn-lg">Create your account <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="landing-footer">
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-lg-4">
                <a href="index.php" class="landing-brand mb-3 d-inline-flex">
                    <span class="brand-icon"><i class="bi bi-wallet2"></i></span>
                    <span><?= htmlspecialchars($p['name']) ?></span>
                </a>
                <p class="small" style="max-width: 20rem;"><?= htmlspecialchars($p['description']) ?></p>
            </div>
            <div class="col-lg-2 col-6">
                <h6 class="footer-heading">Product</h6>
                <a href="#features" class="footer-link">Features</a>
                <a href="#how" class="footer-link">How it works</a>
                <a href="auth/register.php" class="footer-link">Get started</a>
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
<script src="assets/js/app.js"></script>
</body>
</html>
