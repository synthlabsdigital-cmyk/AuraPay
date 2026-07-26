<?php
/**
 * Customer Dashboard
 *
 * Overview of account status, credit score, active loan, recent transactions,
 * and profile completion progress.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';

$userId = Session::userId();
$profile = Database::fetch('SELECT * FROM user_profiles WHERE user_id = :uid', [':uid' => $userId]);
$latestCredit = Database::fetch('SELECT * FROM credit_evaluations WHERE user_id = :uid ORDER BY id DESC LIMIT 1', [':uid' => $userId]);
$activeLoan = Database::fetch('SELECT * FROM loans WHERE user_id = :uid AND status IN (:s1,:s2,:s3) ORDER BY id DESC LIMIT 1', [
    ':uid' => $userId, ':s1' => LOAN_ACTIVE, ':s2' => LOAN_PENDING, ':s3' => LOAN_APPROVED,
]);
$pendingLoan = Database::fetch('SELECT * FROM loans WHERE user_id = :uid AND status IN (:s1,:s2) ORDER BY id DESC LIMIT 1', [
    ':uid' => $userId, ':s1' => LOAN_PENDING, ':s2' => LOAN_UNDER_REVIEW,
]);
$recentTx = Database::fetchAll('SELECT * FROM transactions WHERE user_id = :uid ORDER BY transaction_date DESC LIMIT 5', [':uid' => $userId]);
$docCount = Database::count('SELECT COUNT(*) FROM documents WHERE user_id = :uid', [':uid' => $userId]);

// Profile completion
$completion = 0;
if ($profile) {
    $fields = ['date_of_birth','gender','civil_status','present_address','region','province','city','barangay','postal_code','employment_status','monthly_income','id_type','id_number'];
    $filled = 0;
    foreach ($fields as $f) {
        if (!empty($profile[$f])) $filled++;
    }
    $completion = (int) round(($filled / count($fields)) * 100);
}
?>

<div class="page-header">
    <?= section_label('Overview') ?>
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Your lending portfolio at a glance.</p>
</div>

<!-- Status cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-glow" style="background: rgba(212,175,122,0.15);"></div>
            <div class="stat-body">
                <span class="stat-label">Credit Score</span>
                <span class="stat-value"><?= $latestCredit ? htmlspecialchars($latestCredit['score']) : '—' ?></span>
                <span class="stat-sub"><?= $latestCredit ? htmlspecialchars($latestCredit['rating']) : 'Not evaluated yet' ?></span>
            </div>
            <div class="stat-icon" style="background: rgba(212,175,122,0.08); color: var(--gold);"><i class="bi bi-graph-up-arrow"></i></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-glow" style="background: rgba(52,211,153,0.15);"></div>
            <div class="stat-body">
                <span class="stat-label">Active Loan</span>
                <span class="stat-value"><?= $activeLoan ? Util::formatMoney($activeLoan['outstanding_balance']) : '—' ?></span>
                <span class="stat-sub"><?= $activeLoan ? 'Outstanding' : 'No active loan' ?></span>
            </div>
            <div class="stat-icon" style="background: rgba(52,211,153,0.08); color: var(--emerald);"><i class="bi bi-cash-coin"></i></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-glow" style="background: rgba(96,165,250,0.15);"></div>
            <div class="stat-body">
                <span class="stat-label">Documents</span>
                <span class="stat-value"><?= $docCount ?></span>
                <span class="stat-sub">Uploaded</span>
            </div>
            <div class="stat-icon" style="background: rgba(96,165,250,0.08); color: var(--sky);"><i class="bi bi-file-earmark-text"></i></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-glow" style="background: rgba(251,191,36,0.15);"></div>
            <div class="stat-body">
                <span class="stat-label">Profile</span>
                <span class="stat-value"><?= $completion ?>%</span>
                <span class="stat-sub">Complete</span>
            </div>
            <div class="stat-icon" style="background: rgba(251,191,36,0.08); color: var(--amber);"><i class="bi bi-person-check"></i></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Profile completion -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Profile Completion</h5>
                <span class="badge badge-gold"><?= $completion ?>%</span>
            </div>
            <div class="card-body">
                <div class="progress mb-3" style="height:8px">
                    <div class="progress-bar" style="width:<?= $completion ?>%"></div>
                </div>
                <?php if ($completion < 100): ?>
                    <p class="text-muted small mb-3">Complete your profile to improve your credit evaluation.</p>
                    <a href="profile.php" class="btn btn-ghost-gold btn-sm">Complete profile</a>
                <?php else: ?>
                    <p class="small mb-0" style="color: var(--emerald);"><i class="bi bi-check-circle me-1"></i>Your profile is complete.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Active/pending loan -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Loan Status</h5>
            </div>
            <div class="card-body">
                <?php if ($pendingLoan): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Application <?= htmlspecialchars($pendingLoan['loan_reference']) ?></span>
                        <?= Util::statusBadge($pendingLoan['status']) ?>
                    </div>
                    <p class="text-muted small">Applied on <?= Util::formatDate($pendingLoan['application_date']) ?> for <?= Util::formatMoney($pendingLoan['principal_amount']) ?>.</p>
                    <a href="loan_detail.php?id=<?= $pendingLoan['id'] ?>" class="btn btn-outline-primary btn-sm">View details</a>
                <?php elseif ($activeLoan): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Loan <?= htmlspecialchars($activeLoan['loan_reference']) ?></span>
                        <?= Util::statusBadge($activeLoan['status']) ?>
                    </div>
                    <div class="row g-2 small">
                        <div class="col-6"><span class="text-muted">Principal:</span> <?= Util::formatMoney($activeLoan['principal_amount']) ?></div>
                        <div class="col-6"><span class="text-muted">Monthly:</span> <?= Util::formatMoney($activeLoan['monthly_payment']) ?></div>
                        <div class="col-6"><span class="text-muted">Paid:</span> <?= Util::formatMoney($activeLoan['amount_paid']) ?></div>
                        <div class="col-6"><span class="text-muted">Outstanding:</span> <?= Util::formatMoney($activeLoan['outstanding_balance']) ?></div>
                    </div>
                    <a href="loan_detail.php?id=<?= $activeLoan['id'] ?>" class="btn btn-outline-primary btn-sm mt-3">View details</a>
                <?php else: ?>
                    <p class="text-muted mb-3">You do not have an active loan.</p>
                    <?php if ($latestCredit && $latestCredit['score'] >= 580): ?>
                        <a href="apply_loan.php" class="btn btn-gold btn-sm">Apply for a loan</a>
                    <?php elseif (!$latestCredit): ?>
                        <a href="credit_evaluation.php" class="btn btn-gold btn-sm">Get credit evaluation</a>
                    <?php else: ?>
                        <span class="text-muted small">Your credit score does not yet qualify for a loan.</span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent transactions -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Transactions</h5>
                <a href="transactions.php" class="btn btn-link btn-sm">View all</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentTx)): ?>
                    <p class="text-muted text-center py-4 mb-0">No transactions yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr><th>Reference</th><th>Type</th><th>Amount</th><th>Status</th><th>Date</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentTx as $tx): ?>
                                <tr>
                                    <td><code><?= htmlspecialchars($tx['transaction_reference']) ?></code></td>
                                    <td><?= ucfirst(str_replace('_', ' ', $tx['type'])) ?></td>
                                    <td><?= Util::formatMoney($tx['amount']) ?></td>
                                    <td><?= Util::statusBadge($tx['status']) ?></td>
                                    <td><?= Util::formatDate($tx['transaction_date']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
