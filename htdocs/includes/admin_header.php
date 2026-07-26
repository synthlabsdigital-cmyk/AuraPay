<?php
/**
 * Admin Header
 *
 * Shared <head>, topbar, and sidebar for all admin portal pages.
 * Expects $pageTitle to be set before inclusion.
 */

declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    require_once __DIR__ . '/../config/constants.php';
}

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/product.php';

Session::requireAdmin();

$adminUser = Database::fetch('SELECT * FROM users WHERE id = :id', [':id' => Session::userId()]);
$p = product();
$role = Session::userRole();
$active = basename($_SERVER['PHP_SELF'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin &middot; <?= htmlspecialchars($pageTitle ?? 'Dashboard') ?> &middot; <?= htmlspecialchars($p['name']) ?></title>
    <meta name="csrf-token" content="<?= Csrf::token() ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= BASE_PATH ?>/assets/css/aurapay.css" rel="stylesheet">
</head>
<body>

<div class="admin-wrapper bg-grain">

    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <a href="<?= BASE_PATH ?>/admin/dashboard.php" class="sidebar-brand">
            <span class="brand-icon" style="background: linear-gradient(135deg, var(--obsidian-4), var(--obsidian-3)); color: var(--gold-bright); border: 1px solid rgba(212,175,122,0.3);">
                <i class="bi bi-shield-lock"></i>
            </span>
            <span>
                <span class="brand-text"><?= htmlspecialchars($p['name']) ?></span>
                <span class="brand-sub">Control Center</span>
            </span>
        </a>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Operations</div>
            <a href="<?= BASE_PATH ?>/admin/dashboard.php" class="nav-link <?= $active === 'dashboard.php' ? 'active' : '' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="<?= BASE_PATH ?>/admin/customers.php" class="nav-link <?= $active === 'customers.php' ? 'active' : '' ?>"><i class="bi bi-people"></i> Customers</a>
            <a href="<?= BASE_PATH ?>/admin/applications.php" class="nav-link <?= $active === 'applications.php' ? 'active' : '' ?>"><i class="bi bi-file-earmark-text"></i> Applications</a>
            <a href="<?= BASE_PATH ?>/admin/loans.php" class="nav-link <?= $active === 'loans.php' ? 'active' : '' ?>"><i class="bi bi-cash-stack"></i> Loans</a>
            <a href="<?= BASE_PATH ?>/admin/credit_evaluation.php" class="nav-link <?= $active === 'credit_evaluation.php' ? 'active' : '' ?>"><i class="bi bi-graph-up-arrow"></i> Credit Evaluations</a>
            <a href="<?= BASE_PATH ?>/admin/transactions.php" class="nav-link <?= $active === 'transactions.php' ? 'active' : '' ?>"><i class="bi bi-arrow-left-right"></i> Transactions</a>
            <a href="<?= BASE_PATH ?>/admin/reports.php" class="nav-link <?= $active === 'reports.php' ? 'active' : '' ?>"><i class="bi bi-bar-chart-line"></i> Reports</a>
            <a href="<?= BASE_PATH ?>/admin/activity_logs.php" class="nav-link <?= $active === 'activity_logs.php' ? 'active' : '' ?>"><i class="bi bi-journal-text"></i> Activity Logs</a>
            <?php if (in_array($role, [ROLE_SUPER_ADMIN, ROLE_ADMIN], true)): ?>
            <a href="<?= BASE_PATH ?>/admin/configuration.php" class="nav-link <?= $active === 'configuration.php' ? 'active' : '' ?>"><i class="bi bi-sliders"></i> Configuration</a>
            <a href="<?= BASE_PATH ?>/admin/maintenance.php" class="nav-link <?= $active === 'maintenance.php' ? 'active' : '' ?>"><i class="bi bi-tools"></i> Maintenance</a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <a href="<?= BASE_PATH ?>/admin/logout.php" class="nav-link" style="color: rgba(251,113,133,0.7);">
                <i class="bi bi-box-arrow-right"></i> Sign out
            </a>
        </div>
    </aside>

    <!-- Main -->
    <div class="admin-main">

        <!-- Topbar -->
        <header class="admin-topbar">
            <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list fs-4"></i></button>
            <div class="d-flex align-items-center gap-2">
                <span class="d-none d-md-inline text-muted">Control Center</span>
            </div>
            <div class="topbar-actions">
                <div class="dropdown">
                    <button class="icon-btn d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <span class="avatar avatar-sm avatar-admin"><?= Util::initials(Session::userName()) ?></span>
                        <span class="d-none d-md-inline" style="color: var(--ink); font-weight: 500; font-size: 0.875rem;"><?= htmlspecialchars($adminUser['first_name'] ?? 'Admin') ?></span>
                        <span class="badge badge-neutral"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $role ?? 'admin'))) ?></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item text-danger" href="<?= BASE_PATH ?>/admin/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sign out</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page content -->
        <main class="admin-content animate-fade-in">
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
