<?php
/**
 * Admin Dashboard
 *
 * Overview of key metrics: total customers, active loans, pending applications,
 * total disbursed, total repaid, and recent activity.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/admin_header.php';

$totalCustomers = Database::count("SELECT COUNT(*) FROM users WHERE user_type = 'customer'");
$activeLoans = Database::count("SELECT COUNT(*) FROM loans WHERE status = 'active'");
$pendingApps = Database::count("SELECT COUNT(*) FROM loans WHERE status IN ('pending','under_review')");
$disbursed = Transaction::totalDisbursed();
$repaid = Transaction::totalRepaid();
$totalLoans = Database::count("SELECT COUNT(*) FROM loans");
$completedLoans = Database::count("SELECT COUNT(*) FROM loans WHERE status = 'completed'");
$defaultedLoans = Database::count("SELECT COUNT(*) FROM loans WHERE status = 'defaulted'");

$recentApps = Database::fetchAll(
    'SELECT l.*, u.first_name, u.last_name, u.email
     FROM loans l JOIN users u ON l.user_id = u.id
     ORDER BY l.created_at DESC LIMIT 5'
);
$recentLogs = Database::fetchAll(
    'SELECT a.*, u.first_name AS admin_first, u.last_name AS admin_last
     FROM activity_logs a LEFT JOIN users u ON a.admin_id = u.id
     ORDER BY a.created_at DESC LIMIT 8'
);
?>

<div class="page-header">
    <?= section_label('Overview') ?>
    <h1 class="page-title">Control Center</h1>
    <p class="page-subtitle">Platform overview and recent activity.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-glow" style="background: rgba(212,175,122,0.15);"></div>
            <div class="stat-body">
                <span class="stat-label">Total Customers</span>
                <span class="stat-value"><?= $totalCustomers ?></span>
            </div>
            <div class="stat-icon" style="background: rgba(212,175,122,0.08); color: var(--gold);"><i class="bi bi-people"></i></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-glow" style="background: rgba(251,191,36,0.15);"></div>
            <div class="stat-body">
                <span class="stat-label">Pending Applications</span>
                <span class="stat-value"><?= $pendingApps ?></span>
            </div>
            <div class="stat-icon" style="background: rgba(251,191,36,0.08); color: var(--amber);"><i class="bi bi-file-earmark-text"></i></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-glow" style="background: rgba(52,211,153,0.15);"></div>
            <div class="stat-body">
                <span class="stat-label">Active Loans</span>
                <span class="stat-value"><?= $activeLoans ?></span>
            </div>
            <div class="stat-icon" style="background: rgba(52,211,153,0.08); color: var(--emerald);"><i class="bi bi-cash-stack"></i></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-glow" style="background: rgba(96,165,250,0.15);"></div>
            <div class="stat-body">
                <span class="stat-label">Total Disbursed</span>
                <span class="stat-value"><?= Util::formatMoney($disbursed) ?></span>
            </div>
            <div class="stat-icon" style="background: rgba(96,165,250,0.08); color: var(--sky);"><i class="bi bi-graph-up"></i></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card stat-mini"><div class="stat-label">Total Repaid</div><div class="stat-value"><?= Util::formatMoney($repaid) ?></div></div></div>
    <div class="col-md-3"><div class="card stat-mini"><div class="stat-label">Total Loans</div><div class="stat-value"><?= $totalLoans ?></div></div></div>
    <div class="col-md-3"><div class="card stat-mini"><div class="stat-label">Completed Loans</div><div class="stat-value"><?= $completedLoans ?></div></div></div>
    <div class="col-md-3"><div class="card stat-mini"><div class="stat-label">Defaulted Loans</div><div class="stat-value" style="color: var(--rose);"><?= $defaultedLoans ?></div></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Loan Applications</h5>
                <a href="applications.php" class="btn btn-link btn-sm">View all</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Reference</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                        <tbody>
                            <?php foreach ($recentApps as $loan): ?>
                            <tr>
                                <td><code><?= htmlspecialchars($loan['loan_reference']) ?></code></td>
                                <td><?= htmlspecialchars($loan['first_name'] . ' ' . $loan['last_name']) ?></td>
                                <td><?= Util::formatMoney($loan['principal_amount']) ?></td>
                                <td><?= Util::statusBadge($loan['status']) ?></td>
                                <td><?= Util::formatDate($loan['application_date']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Activity</h5>
                <a href="activity_logs.php" class="btn btn-link btn-sm">View all</a>
            </div>
            <div class="card-body">
                <?php foreach ($recentLogs as $log): ?>
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="w-2 h-2 rounded-circle mt-1.5 flex-shrink-0" style="width:8px;height:8px;background: <?= $log['severity']==='critical'?'var(--rose)':($log['severity']==='warning'?'var(--amber)':'var(--sky)') ?>"></div>
                    <div class="flex-grow-1">
                        <div class="small" style="color: var(--ink-2);"><?= htmlspecialchars($log['description']) ?></div>
                        <div class="text-muted" style="font-size:.75rem"><?= Util::timeAgo($log['created_at']) ?> &middot; <?= htmlspecialchars($log['admin_first'] ?? 'System') ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
