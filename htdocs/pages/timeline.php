<?php
/**
 * Timeline Page
 *
 * Chronological view of the customer's journey: registration, profile,
 * documents, credit evaluations, loan applications, and transactions.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Timeline';
require_once __DIR__ . '/../includes/header.php';

$userId = Session::userId();

// Build timeline from multiple sources
$events = [];

$user = Database::fetch('SELECT * FROM users WHERE id = :id', [':id' => $userId]);
$events[] = ['date' => $user['created_at'], 'icon' => 'person-plus', 'title' => 'Account created', 'desc' => 'You registered your account.', 'color' => 'primary'];

if ($user['email_verified_at']) {
    $events[] = ['date' => $user['email_verified_at'], 'icon' => 'envelope-check', 'title' => 'Email verified', 'desc' => 'Your email address was verified.', 'color' => 'success'];
}
if ($user['last_login_at']) {
    $events[] = ['date' => $user['last_login_at'], 'icon' => 'box-arrow-in-right', 'title' => 'Last login', 'desc' => 'You signed in to your account.', 'color' => 'info'];
}

$profile = Database::fetch('SELECT * FROM user_profiles WHERE user_id = :uid', [':uid' => $userId]);
if ($profile && $profile['completed_at']) {
    $events[] = ['date' => $profile['completed_at'], 'icon' => 'person-check', 'title' => 'Profile completed', 'desc' => 'You completed your profile information.', 'color' => 'success'];
}

$docs = Database::fetchAll('SELECT * FROM documents WHERE user_id = :uid ORDER BY created_at', [':uid' => $userId]);
foreach ($docs as $doc) {
    $events[] = ['date' => $doc['created_at'], 'icon' => 'file-earmark-plus', 'title' => 'Document uploaded', 'desc' => ucfirst(str_replace('_',' ',$doc['document_type'])), 'color' => 'info'];
    if ($doc['verified_at']) {
        $events[] = ['date' => $doc['verified_at'], 'icon' => 'patch-check', 'title' => 'Document ' . $doc['status'], 'desc' => ucfirst(str_replace('_',' ',$doc['document_type'])) . ' was ' . $doc['status'], 'color' => $doc['status'] === 'verified' ? 'success' : 'danger'];
    }
}

$evals = Database::fetchAll('SELECT * FROM credit_evaluations WHERE user_id = :uid ORDER BY created_at', [':uid' => $userId]);
foreach ($evals as $ev) {
    $events[] = ['date' => $ev['evaluated_at'] ?? $ev['created_at'], 'icon' => 'graph-up-arrow', 'title' => 'Credit evaluation', 'desc' => "Score: {$ev['score']} ({$ev['rating']})", 'color' => 'primary'];
}

$loans = Database::fetchAll('SELECT * FROM loans WHERE user_id = :uid ORDER BY created_at', [':uid' => $userId]);
foreach ($loans as $loan) {
    $events[] = ['date' => $loan['application_date'], 'icon' => 'cash-coin', 'title' => 'Loan applied', 'desc' => "{$loan['loan_reference']} for " . Util::formatMoney($loan['principal_amount']), 'color' => 'primary'];
    if ($loan['approval_date']) {
        $events[] = ['date' => $loan['approval_date'], 'icon' => 'check-circle', 'title' => 'Loan approved', 'desc' => "{$loan['loan_reference']} was approved", 'color' => 'success'];
    }
    if ($loan['rejection_date']) {
        $events[] = ['date' => $loan['rejection_date'], 'icon' => 'x-circle', 'title' => 'Loan rejected', 'desc' => "{$loan['loan_reference']} was rejected", 'color' => 'danger'];
    }
    if ($loan['disbursement_date']) {
        $events[] = ['date' => $loan['disbursement_date'], 'icon' => 'bank', 'title' => 'Loan disbursed', 'desc' => "{$loan['loan_reference']} was disbursed", 'color' => 'success'];
    }
    if ($loan['completed_date']) {
        $events[] = ['date' => $loan['completed_date'], 'icon' => 'trophy', 'title' => 'Loan completed', 'desc' => "{$loan['loan_reference']} was fully paid", 'color' => 'success'];
    }
}

$txs = Database::fetchAll('SELECT * FROM transactions WHERE user_id = :uid ORDER BY transaction_date', [':uid' => $userId]);
foreach ($txs as $tx) {
    $events[] = ['date' => $tx['transaction_date'], 'icon' => $tx['type'] === 'disbursement' ? 'bank' : 'credit-card', 'title' => ucfirst($tx['type']), 'desc' => $tx['description'] . ' — ' . Util::formatMoney($tx['amount']), 'color' => $tx['type'] === 'repayment' ? 'success' : 'info'];
}

// Sort by date descending
usort($events, fn($a, $b) => strtotime($b['date']) <=> strtotime($a['date']));
?>

<div class="page-header">
    <?= section_label('Journey') ?>
    <h1 class="page-title">Timeline</h1>
    <p class="page-subtitle">Your complete journey with <?= htmlspecialchars(product()['name']) ?>.</p>
</div>

<?php if (empty($events)): ?>
    <p class="text-muted">No activity yet.</p>
<?php else: ?>
    <div class="timeline">
        <?php foreach ($events as $e): ?>
        <div class="timeline-item">
            <div class="timeline-icon" style="background: <?= $e['color']==='primary'?'rgba(212,175,122,0.1)':($e['color']==='success'?'rgba(52,211,153,0.1)':'rgba(96,165,250,0.1)') ?>; color: <?= $e['color']==='primary'?'var(--gold-bright)':($e['color']==='success'?'var(--emerald)':'var(--sky)') ?>; border-color: <?= $e['color']==='primary'?'rgba(212,175,122,0.3)':($e['color']==='success'?'rgba(52,211,153,0.3)':'rgba(96,165,250,0.3)') ?>;">
                <i class="bi bi-<?= $e['icon'] ?>"></i>
            </div>
            <div class="timeline-content">
                <div class="timeline-date"><?= Util::formatDateTime($e['date']) ?></div>
                <h6 class="timeline-title"><?= htmlspecialchars($e['title']) ?></h6>
                <p class="timeline-desc text-muted"><?= htmlspecialchars($e['desc']) ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
