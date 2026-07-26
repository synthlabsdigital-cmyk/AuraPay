<?php
/**
 * Auth Header
 *
 * Split-screen layout: editorial panel (left) + form panel (right).
 * Shared <head> for unauthenticated pages (login, register, verify_otp, etc.).
 * Set $authVariant to 'admin' for the admin login variant.
 */

declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    require_once __DIR__ . '/../config/constants.php';
}

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/product.php';

$p = product();
$variant = $authVariant ?? 'customer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Auth') ?> &middot; <?= htmlspecialchars($p['name']) ?></title>
    <meta name="csrf-token" content="<?= Csrf::token() ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= BASE_PATH ?>/assets/css/aurapay.css" rel="stylesheet">
</head>
<body class="auth-body">

<div class="auth-split">
    <!-- Editorial panel -->
    <div class="auth-editorial d-none d-lg-flex">
        <div class="glow-1"></div>
        <div class="glow-2"></div>

        <div class="auth-editorial-content">
            <a href="<?= BASE_PATH ?>/index.php" class="auth-brand-link">
                <span class="auth-brand-icon">
                    <i class="bi <?= $variant === 'admin' ? 'bi-shield-check' : 'bi-wallet2' ?>"></i>
                </span>
                <span>
                    <span class="auth-brand-text"><?= htmlspecialchars($p['name']) ?></span>
                    <span class="auth-brand-sub"><?= $variant === 'admin' ? 'Control Center' : 'Private Client' ?></span>
                </span>
            </a>
        </div>

        <div class="auth-editorial-content">
            <div class="ornament mb-4"></div>
            <h2>
                <?= $variant === 'admin'
                    ? 'Governing the<br><span class="gold-text">flow of capital.</span>'
                    : 'Borrow with<br><span class="gold-text">quiet confidence.</span>' ?>
            </h2>
            <p class="mb-5">
                <?= $variant === 'admin'
                    ? 'A unified control center for portfolio management, risk assessment, and operational integrity.'
                    : 'Transparent terms. Fair evaluation. A lending experience designed around your dignity.' ?>
            </p>
            <div>
                <div class="auth-feature">
                    <span class="auth-feature-icon"><i class="bi bi-lock"></i></span>
                    Bank-grade encryption
                </div>
                <div class="auth-feature">
                    <span class="auth-feature-icon"><i class="bi bi-graph-up-arrow"></i></span>
                    Real-time credit assessment
                </div>
                <div class="auth-feature">
                    <span class="auth-feature-icon"><i class="bi bi-shield-check"></i></span>
                    Regulatory compliance
                </div>
            </div>
        </div>

        <div class="auth-editorial-content auth-quote">
            <div class="quote-mark"><i class="bi bi-quote"></i></div>
            <p>
                <?= $variant === 'admin'
                    ? 'The control center transformed how we manage risk across our entire portfolio.'
                    : 'The most dignified lending experience I have ever encountered.' ?>
            </p>
            <div class="d-flex align-items-center gap-2">
                <span class="avatar avatar-sm"></span>
                <div>
                    <div style="color: var(--ink-2); font-weight: 500; font-size: 0.875rem;"><?= $variant === 'admin' ? 'Reginald Cruz' : 'Maria Santos' ?></div>
                    <div style="color: var(--ink-4); font-size: 0.75rem;"><?= $variant === 'admin' ? 'Chief Risk Officer' : 'Quezon City' ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form panel -->
    <div class="auth-form-panel">
        <div class="auth-wrapper">
            <!-- Mobile brand -->
            <div class="d-lg-none d-flex align-items-center justify-content-center gap-3 mb-5">
                <span class="auth-brand-icon">
                    <i class="bi <?= $variant === 'admin' ? 'bi-shield-check' : 'bi-wallet2' ?>"></i>
                </span>
                <span class="auth-brand-text"><?= htmlspecialchars($p['name']) ?></span>
            </div>

            <div class="mb-4">
                <div class="ornament mb-3"></div>
                <h1 class="auth-title"><?= htmlspecialchars($pageTitle ?? 'Welcome') ?></h1>
                <p class="auth-subtitle"><?= htmlspecialchars($authSubtitle ?? 'Sign in to your account.') ?></p>
            </div>

            <?php if (Session::hasFlash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= htmlspecialchars(Session::flash('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (Session::hasFlash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars(Session::flash('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (Session::hasFlash('info')): ?>
                <div class="alert alert-info alert-dismissible fade show">
                    <?= htmlspecialchars(Session::flash('info')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
