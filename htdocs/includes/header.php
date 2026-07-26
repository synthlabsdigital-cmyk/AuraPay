<?php
/**
 * Customer Header
 *
 * Shared <head>, navbar, and sidebar for all customer-facing pages.
 * Expects $pageTitle to be set before inclusion.
 */

declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    require_once __DIR__ . '/../config/constants.php';
}

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/product.php';

Session::requireCustomer();

$currentUser = Database::fetch('SELECT * FROM users WHERE id = :id', [':id' => Session::userId()]);
$unreadNotifs = Notification::unreadCount((int) Session::userId());
$p = product();
$notifList = Notification::forUser((int) Session::userId(), 5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?> &middot; <?= htmlspecialchars($p['name']) ?></title>
    <meta name="csrf-token" content="<?= Csrf::token() ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= BASE_PATH ?>/assets/css/aurapay.css" rel="stylesheet">
</head>
<body>

<div class="app-wrapper bg-grain">

    <!-- Sidebar -->
    <aside class="app-sidebar" id="appSidebar">
        <a href="<?= BASE_PATH ?>/index.php" class="sidebar-brand">
            <span class="brand-icon"><i class="bi bi-wallet2"></i></span>
            <span>
                <span class="brand-text"><?= htmlspecialchars($p['name']) ?></span>
                <span class="brand-sub">Private Client</span>
            </span>
        </a>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Account</div>
            <a href="<?= BASE_PATH ?>/dashboard.php" class="nav-link <?= activeNav('dashboard.php') ?>"><i class="bi bi-grid-1x2"></i> Dashboard</a>
            <a href="<?= BASE_PATH ?>/profile.php" class="nav-link <?= activeNav('profile.php') ?>"><i class="bi bi-person-circle"></i> Profile</a>
            <a href="<?= BASE_PATH ?>/documents.php" class="nav-link <?= activeNav('documents.php') ?>"><i class="bi bi-file-earmark-text"></i> Documents</a>
            <a href="<?= BASE_PATH ?>/credit_evaluation.php" class="nav-link <?= activeNav('credit_evaluation.php') ?>"><i class="bi bi-graph-up-arrow"></i> Credit Evaluation</a>
            <a href="<?= BASE_PATH ?>/apply_loan.php" class="nav-link <?= activeNav('apply_loan.php') ?>"><i class="bi bi-cash-coin"></i> Apply for Loan</a>
            <a href="<?= BASE_PATH ?>/loan_history.php" class="nav-link <?= activeNav('loan_history.php') ?>"><i class="bi bi-clock-history"></i> Loan History</a>
            <a href="<?= BASE_PATH ?>/payments.php" class="nav-link <?= activeNav('payments.php') ?>"><i class="bi bi-credit-card"></i> Payments</a>
            <a href="<?= BASE_PATH ?>/transactions.php" class="nav-link <?= activeNav('transactions.php') ?>"><i class="bi bi-arrow-left-right"></i> Transactions</a>
            <a href="<?= BASE_PATH ?>/timeline.php" class="nav-link <?= activeNav('timeline.php') ?>"><i class="bi bi-diagram-3"></i> Timeline</a>
            <a href="<?= BASE_PATH ?>/notifications.php" class="nav-link <?= activeNav('notifications.php') ?>">
                <i class="bi bi-bell"></i> Notifications
                <?php if ($unreadNotifs > 0): ?>
                    <span class="badge badge-gold ms-auto"><?= $unreadNotifs ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= BASE_PATH ?>/settings.php" class="nav-link <?= activeNav('settings.php') ?>"><i class="bi bi-gear"></i> Settings</a>
        </nav>

        <div class="sidebar-footer">
            <a href="<?= BASE_PATH ?>/auth/logout.php" class="nav-link" style="color: rgba(251,113,133,0.7);">
                <i class="bi bi-box-arrow-right"></i> Sign out
            </a>
        </div>
    </aside>

    <!-- Main -->
    <div class="app-main">

        <!-- Topbar -->
        <header class="app-topbar">
            <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list fs-4"></i></button>

            <div class="d-none d-md-flex align-items-center gap-2">
                <span class="text-muted">Welcome,</span>
                <span style="color: var(--ink); font-weight: 500;"><?= htmlspecialchars($currentUser['first_name'] ?? 'User') ?></span>
            </div>

            <div class="topbar-actions">
                <!-- Notifications -->
                <div class="dropdown">
                    <button class="icon-btn position-relative" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-5"></i>
                        <?php if ($unreadNotifs > 0): ?><span class="dot-indicator"></span><?php endif; ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" style="min-width: 320px;">
                        <h6 class="dropdown-header">Notifications</h6>
                        <?php if (empty($notifList)): ?>
                            <div class="dropdown-item text-muted">No notifications</div>
                        <?php else: foreach ($notifList as $n): ?>
                            <a class="dropdown-item <?= $n['is_read'] ? '' : 'fw-semibold' ?>" href="<?= BASE_PATH ?>/notifications.php">
                                <div class="d-flex justify-content-between align-items-start">
                                    <span style="font-size: 0.875rem; color: var(--ink);"><?= htmlspecialchars($n['title']) ?></span>
                                </div>
                                <small class="text-muted d-block mt-1"><?= Util::timeAgo($n['created_at']) ?></small>
                            </a>
                        <?php endforeach; endif; ?>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-center small" href="<?= BASE_PATH ?>/notifications.php" style="color: var(--gold-bright);">View all</a>
                    </div>
                </div>

                <!-- User menu -->
                <div class="dropdown">
                    <button class="icon-btn d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <span class="avatar avatar-sm"><?= Util::initials(Session::userName()) ?></span>
                        <span class="d-none d-md-inline" style="color: var(--ink); font-weight: 500; font-size: 0.875rem;"><?= htmlspecialchars($currentUser['first_name'] ?? '') ?></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="<?= BASE_PATH ?>/profile.php"><i class="bi bi-person me-2"></i>My Profile</a>
                        <a class="dropdown-item" href="<?= BASE_PATH ?>/settings.php"><i class="bi bi-gear me-2"></i>Settings</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="<?= BASE_PATH ?>/auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sign out</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page content -->
        <main class="app-content animate-fade-in">
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
