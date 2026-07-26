<?php
/**
 * Admin Loan Detail Page
 *
 * Full loan details with amortization, transactions, and admin actions
 * (review, approve, reject, disburse, record payment).
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Loan Details';
require_once __DIR__ . '/../includes/admin_header.php';

$adminId = Session::userId();
$loanId = (int) ($_GET['id'] ?? 0);
$loan = Database::fetch(
    'SELECT l.*, u.first_name, u.last_name, u.email, u.phone
     FROM loans l JOIN users u ON l.user_id = u.id WHERE l.id = :id',
    [':id' => $loanId]
);
if (!$loan) {
    Session::flash('error', 'Loan not found.');
    Redirect::to('admin_loans');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();
    $action = $_POST['action'] ?? '';

    if ($action === 'review') {
        $r = Loan::review($loanId, $adminId);
    } elseif ($action === 'approve') {
        $r = Loan::approve($loanId, $adminId, trim($_POST['notes'] ?? ''));
    } elseif ($action === 'reject') {
        $r = Loan::reject($loanId, $adminId, trim($_POST['reason'] ?? ''));
    } elseif ($action === 'disburse') {
        $r = Loan::disburse($loanId, $adminId, $_POST['method'] ?? '', trim($_POST['reference'] ?? ''));
    } elseif ($action === 'payment') {
        $amount = (float) ($_POST['amount'] ?? 0);
        $method = $_POST['method'] ?? '';
        $ref = trim($_POST['reference'] ?? '');
        $r = Loan::processPayment($loanId, $amount, $method, $ref, $adminId);
    }
    Session::flash($r['success']?'success':'error', $r['message'] ?? 'Done.');
    Redirect::to('admin_loans', ['id' => $loanId]);
}

$amortization = Loan::getAmortization($loanId);
$transactions = Transaction::forLoan($loanId);
$paymentMethods = ['cash'=>'Cash','bank_transfer'=>'Bank Transfer','gcash'=>'GCash','maya'=>'Maya','over_the_counter'=>'Over the Counter'];
?>

<div class="page-header">
    <?= section_label('Loan') ?>
    <h1 class="page-title">Loan <?= htmlspecialchars($loan['loan_reference']) ?></h1>
    <p class="page-subtitle"><?= htmlspecialchars($loan['first_name'].' '.$loan['last_name']) ?> &middot; <?= Util::statusBadge($loan['status']) ?></p>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card stat-mini"><div class="stat-label">Principal</div><div class="stat-value"><?= Util::formatMoney($loan['principal_amount']) ?></div></div></div>
    <div class="col-md-3"><div class="card stat-mini"><div class="stat-label">Monthly</div><div class="stat-value"><?= Util::formatMoney($loan['monthly_payment']) ?></div></div></div>
    <div class="col-md-3"><div class="card stat-mini"><div class="stat-label">Total Payable</div><div class="stat-value"><?= Util::formatMoney($loan['total_payable']) ?></div></div></div>
    <div class="col-md-3"><div class="card stat-mini"><div class="stat-label">Outstanding</div><div class="stat-value gold-text"><?= Util::formatMoney($loan['outstanding_balance']) ?></div></div></div>
</div>

<!-- Admin actions -->
<div class="card mb-3">
    <div class="card-header"><h5 class="mb-0">Admin Actions</h5></div>
    <div class="card-body">
        <?php if ($loan['status'] === LOAN_PENDING): ?>
        <form method="post" class="d-inline">
            <?= Csrf::field() ?>
            <input type="hidden" name="action" value="review">
            <button type="submit" class="btn btn-ghost-gold" style="color: var(--sky); border-color: rgba(96,165,250,0.3);">Move to Review</button>
        </form>
        <?php endif; ?>
        <?php if (in_array($loan['status'], [LOAN_PENDING, LOAN_UNDER_REVIEW], true)): ?>
        <button type="button" class="btn btn-emerald" data-bs-toggle="modal" data-bs-target="#approveModal">Approve</button>
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">Reject</button>
        <?php endif; ?>
        <?php if ($loan['status'] === LOAN_APPROVED): ?>
        <button type="button" class="btn btn-gold" data-bs-toggle="modal" data-bs-target="#disburseModal">Disburse</button>
        <?php endif; ?>
        <?php if (in_array($loan['status'], [LOAN_ACTIVE, LOAN_DEFAULTED], true)): ?>
        <button type="button" class="btn btn-emerald" data-bs-toggle="modal" data-bs-target="#paymentModal">Record Payment</button>
        <?php endif; ?>
    </div>
</div>

<!-- Amortization -->
<div class="card mb-3">
    <div class="card-header"><h5 class="mb-0">Repayment Schedule</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr><th>#</th><th>Due</th><th>Installment</th><th>Penalty</th><th>Paid</th><th>Balance</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($amortization as $inst): ?>
                    <tr>
                        <td><?= $inst['installment_number'] ?></td>
                        <td><?= Util::formatDate($inst['due_date']) ?></td>
                        <td><?= Util::formatMoney($inst['installment_amount']) ?></td>
                        <td><?= $inst['penalty_amount']>0?Util::formatMoney($inst['penalty_amount']):'—' ?></td>
                        <td><?= Util::formatMoney($inst['paid_amount']) ?></td>
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
<div class="card mb-3">
    <div class="card-header"><h5 class="mb-0">Transactions</h5></div>
    <div class="card-body p-0">
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
                    <?php if (empty($transactions)): ?><tr><td colspan="6" class="text-muted text-center py-3">No transactions.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="approveModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="post">
    <?= Csrf::field() ?>
    <div class="modal-header"><h5 class="modal-title">Approve Loan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><label class="form-label">Notes (optional)</label><textarea name="notes" class="form-control" rows="3"></textarea></div>
    <div class="modal-footer"><input type="hidden" name="action" value="approve"><button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-emerald">Approve</button></div>
</form></div></div></div>

<div class="modal fade" id="rejectModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="post">
    <?= Csrf::field() ?>
    <div class="modal-header"><h5 class="modal-title">Reject Loan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><label class="form-label">Reason <span class="text-danger">*</span></label><textarea name="reason" class="form-control" rows="3" required></textarea></div>
    <div class="modal-footer"><input type="hidden" name="action" value="reject"><button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger">Reject</button></div>
</form></div></div></div>

<div class="modal fade" id="disburseModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="post">
    <?= Csrf::field() ?>
    <div class="modal-header"><h5 class="modal-title">Disburse Loan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <label class="form-label">Method</label>
        <select name="method" class="form-select" required>
            <?php foreach (['bank_transfer'=>'Bank Transfer','gcash'=>'GCash','maya'=>'Maya','cash'=>'Cash','over_the_counter'=>'Over the Counter'] as $k=>$v): ?><option value="<?= $k ?>"><?= $v ?></option><?php endforeach; ?>
        </select>
        <label class="form-label mt-2">Reference (optional)</label>
        <input type="text" name="reference" class="form-control">
    </div>
    <div class="modal-footer"><input type="hidden" name="action" value="disburse"><button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-gold">Disburse</button></div>
</form></div></div></div>

<div class="modal fade" id="paymentModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="post">
    <?= Csrf::field() ?>
    <div class="modal-header"><h5 class="modal-title">Record Payment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <label class="form-label">Amount (₱)</label>
        <input type="number" name="amount" class="form-control" step="0.01" min="1" required>
        <label class="form-label mt-2">Method</label>
        <select name="method" class="form-select" required>
            <?php foreach ($paymentMethods as $k=>$v): ?><option value="<?= $k ?>"><?= $v ?></option><?php endforeach; ?>
        </select>
        <label class="form-label mt-2">Reference (optional)</label>
        <input type="text" name="reference" class="form-control">
    </div>
    <div class="modal-footer"><input type="hidden" name="action" value="payment"><button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-emerald">Record</button></div>
</form></div></div></div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
