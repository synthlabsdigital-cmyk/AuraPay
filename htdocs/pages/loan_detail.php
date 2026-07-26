<?php
/**
 * Loan Detail Page
 *
 * Shows full loan information, amortization schedule, and transactions.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Loan Details';
require_once __DIR__ . '/../includes/header.php';

$userId = Session::userId();
$loanId = (int) ($_GET['id'] ?? 0);
$loan = Database::fetch('SELECT * FROM loans WHERE id = :id AND user_id = :uid', [':id' => $loanId, ':uid' => $userId]);

if (!$loan) {
    Session::flash('error', 'Loan not found.');
    Redirect::to('loan_history');
}

$amortization = Loan::getAmortization($loanId);
$transactions = Transaction::forLoan($loanId);
?>

<div class="page-header">
    <?= section_label('Loan Detail') ?>
    <h1 class="page-title">Loan <?= htmlspecialchars($loan['loan_reference']) ?></h1>
    <p class="page-subtitle">Applied on <?= Util::formatDateTime($loan['application_date']) ?> &middot; <?= Util::statusBadge($loan['status']) ?></p>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card stat-mini"><div class="stat-label">Principal</div><div class="stat-value"><?= Util::formatMoney($loan['principal_amount']) ?></div></div></div>
    <div class="col-md-3"><div class="card stat-mini"><div class="stat-label">Interest Rate</div><div class="stat-value"><?= $loan['interest_rate'] ?>%/mo</div></div></div>
    <div class="col-md-3"><div class="card stat-mini"><div class="stat-label">Term</div><div class="stat-value"><?= $loan['term_months'] ?> months</div></div></div>
    <div class="col-md-3"><div class="card stat-mini"><div class="stat-label">Monthly Payment</div><div class="stat-value"><?= Util::formatMoney($loan['monthly_payment']) ?></div></div></div>
    <div class="col-md-3"><div class="card stat-mini"><div class="stat-label">Total Interest</div><div class="stat-value"><?= Util::formatMoney($loan['total_interest']) ?></div></div></div>
    <div class="col-md-3"><div class="card stat-mini"><div class="stat-label">Processing Fee</div><div class="stat-value"><?= Util::formatMoney($loan['processing_fee']) ?></div></div></div>
    <div class="col-md-3"><div class="card stat-mini"><div class="stat-label">Total Payable</div><div class="stat-value"><?= Util::formatMoney($loan['total_payable']) ?></div></div></div>
    <div class="col-md-3"><div class="card stat-mini"><div class="stat-label">Outstanding</div><div class="stat-value gold-text"><?= Util::formatMoney($loan['outstanding_balance']) ?></div></div></div>
</div>

<?php if ($loan['rejection_reason']): ?>
    <div class="alert alert-danger"><strong>Rejection reason:</strong> <?= htmlspecialchars($loan['rejection_reason']) ?></div>
<?php endif; ?>

<?php if ($loan['disbursement_date']): ?>
<div class="row g-2 mb-3 small">
    <div class="col-md-4"><span class="text-muted">Disbursed:</span> <?= Util::formatDateTime($loan['disbursement_date']) ?></div>
    <div class="col-md-4"><span class="text-muted">Method:</span> <?= ucfirst(str_replace('_',' ',$loan['disbursement_method'] ?? '')) ?></div>
    <div class="col-md-4"><span class="text-muted">First payment:</span> <?= Util::formatDate($loan['first_payment_date']) ?></div>
</div>
<?php endif; ?>

<!-- Amortization -->
<div class="card mb-3">
    <div class="card-header"><h5 class="mb-0">Repayment Schedule</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead>
                    <tr><th>#</th><th>Due Date</th><th>Installment</th><th>Principal</th><th>Interest</th><th>Penalty</th><th>Balance</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($amortization as $inst): ?>
                    <tr>
                        <td><?= $inst['installment_number'] ?></td>
                        <td><?= Util::formatDate($inst['due_date']) ?></td>
                        <td><?= Util::formatMoney($inst['installment_amount']) ?></td>
                        <td><?= Util::formatMoney($inst['principal_component']) ?></td>
                        <td><?= Util::formatMoney($inst['interest_component']) ?></td>
                        <td><?= $inst['penalty_amount'] > 0 ? Util::formatMoney($inst['penalty_amount']) : '—' ?></td>
                        <td><?= Util::formatMoney($inst['balance_after']) ?></td>
                        <td><?= Util::statusBadge($inst['status']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Transactions -->
<div class="card">
    <div class="card-header"><h5 class="mb-0">Transactions</h5></div>
    <div class="card-body p-0">
        <?php if (empty($transactions)): ?>
            <p class="text-muted text-center py-3 mb-0">No transactions for this loan yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Reference</th><th>Type</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php foreach ($transactions as $tx): ?>
                        <tr>
                            <td><code><?= htmlspecialchars($tx['transaction_reference']) ?></code></td>
                            <td><?= ucfirst($tx['type']) ?></td>
                            <td><?= Util::formatMoney($tx['amount']) ?></td>
                            <td><?= ucfirst(str_replace('_',' ',$tx['payment_method'] ?? '')) ?></td>
                            <td><?= Util::statusBadge($tx['status']) ?></td>
                            <td><?= Util::formatDateTime($tx['transaction_date']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
