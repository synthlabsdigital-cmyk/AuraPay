<?php
/**
 * Admin Reports Page
 *
 * Aggregate reports: portfolio summary, loan status breakdown,
 * disbursement vs repayment, and monthly trends.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Reports';
require_once __DIR__ . '/../includes/admin_header.php';

// Portfolio summary
$totalDisbursed = Transaction::totalDisbursed();
$totalRepaid = Transaction::totalRepaid();
$outstanding = (float) Database::fetch("SELECT COALESCE(SUM(outstanding_balance),0) AS v FROM loans WHERE status='active'")['v'];
$totalInterest = (float) Database::fetch("SELECT COALESCE(SUM(total_interest),0) AS v FROM loans WHERE status IN ('active','completed')")['v'];
$totalFees = (float) Database::fetch("SELECT COALESCE(SUM(processing_fee),0) AS v FROM loans WHERE status IN ('active','completed')")['v'];

// Loan status breakdown
$statusCounts = Database::fetchAll(
    "SELECT status, COUNT(*) AS cnt FROM loans GROUP BY status ORDER BY cnt DESC"
);

// Monthly trend (last 6 months)
$trend = Database::fetchAll(
    "SELECT DATE_FORMAT(transaction_date,'%Y-%m') AS m,
            SUM(CASE WHEN type='disbursement' THEN amount ELSE 0 END) AS disbursed,
            SUM(CASE WHEN type='repayment' THEN amount ELSE 0 END) AS repaid
     FROM transactions WHERE status='completed'
       AND transaction_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
     GROUP BY m ORDER BY m"
);

// Top customers by loan count
$topCustomers = Database::fetchAll(
    "SELECT u.id, u.first_name, u.last_name, u.email, COUNT(l.id) AS loan_count, COALESCE(SUM(l.principal_amount),0) AS total_borrowed
     FROM users u LEFT JOIN loans l ON u.id = l.user_id
     WHERE u.user_type='customer'
     GROUP BY u.id ORDER BY loan_count DESC, total_borrowed DESC LIMIT 10"
);
?>

<div class="page-header">
    <?= section_label('Analytics') ?>
    <h1 class="page-title">Reports</h1>
    <p class="page-subtitle">Portfolio performance and analytics.</p>
</div>

<!-- Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-glow" style="background: rgba(212,175,122,0.15);"></div>
            <div class="stat-body"><span class="stat-label">Total Disbursed</span><span class="stat-value"><?= Util::formatMoney($totalDisbursed) ?></span></div>
            <div class="stat-icon" style="background: rgba(212,175,122,0.08); color: var(--gold);"><i class="bi bi-bank"></i></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-glow" style="background: rgba(52,211,153,0.15);"></div>
            <div class="stat-body"><span class="stat-label">Total Repaid</span><span class="stat-value"><?= Util::formatMoney($totalRepaid) ?></span></div>
            <div class="stat-icon" style="background: rgba(52,211,153,0.08); color: var(--emerald);"><i class="bi bi-cash-coin"></i></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-glow" style="background: rgba(251,191,36,0.15);"></div>
            <div class="stat-body"><span class="stat-label">Outstanding</span><span class="stat-value"><?= Util::formatMoney($outstanding) ?></span></div>
            <div class="stat-icon" style="background: rgba(251,191,36,0.08); color: var(--amber);"><i class="bi bi-exclamation-circle"></i></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-glow" style="background: rgba(96,165,250,0.15);"></div>
            <div class="stat-body"><span class="stat-label">Interest Earned</span><span class="stat-value"><?= Util::formatMoney($totalInterest) ?></span></div>
            <div class="stat-icon" style="background: rgba(96,165,250,0.08); color: var(--sky);"><i class="bi bi-graph-up"></i></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Loan status breakdown -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header"><h5 class="mb-0">Loans by Status</h5></div>
            <div class="card-body">
                <?php foreach ($statusCounts as $sc): ?>
                <div class="mb-2">
                    <div class="d-flex justify-content-between small mb-1">
                        <span><?= ucfirst(str_replace('_',' ',$sc['status'])) ?></span>
                        <span class="fw-semibold" style="color: var(--gold-bright);"><?= $sc['cnt'] ?></span>
                    </div>
                    <div class="progress" style="height:6px">
                        <div class="progress-bar" style="width:<?= $sc['cnt']>0?($sc['cnt']/max(1,array_sum(array_column($statusCounts,'cnt')))*100):0 ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($statusCounts)): ?><p class="text-muted">No data.</p><?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Monthly trend -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header"><h5 class="mb-0">Monthly Trend (6 months)</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Month</th><th>Disbursed</th><th>Repaid</th></tr></thead>
                        <tbody>
                            <?php foreach ($trend as $tr): ?>
                            <tr>
                                <td><?= date('M Y', strtotime($tr['m'].'-01')) ?></td>
                                <td><?= Util::formatMoney($tr['disbursed']) ?></td>
                                <td><?= Util::formatMoney($tr['repaid']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($trend)): ?><tr><td colspan="3" class="text-muted text-center py-3">No data.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top customers -->
    <div class="col-12">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Top Customers</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Customer</th><th>Email</th><th>Loans</th><th>Total Borrowed</th></tr></thead>
                        <tbody>
                            <?php foreach ($topCustomers as $tc): ?>
                            <tr>
                                <td><?= htmlspecialchars($tc['first_name'].' '.$tc['last_name']) ?></td>
                                <td><?= htmlspecialchars($tc['email']) ?></td>
                                <td><?= $tc['loan_count'] ?></td>
                                <td class="gold-text"><?= Util::formatMoney($tc['total_borrowed']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($topCustomers)): ?><tr><td colspan="4" class="text-muted text-center py-3">No data.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
