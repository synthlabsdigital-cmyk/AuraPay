<?php
/**
 * Payments Page
 *
 * Shows upcoming installments and allows the customer to make a payment
 * on an active loan.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Payments';
require_once __DIR__ . '/../includes/header.php';

$userId = Session::userId();
$activeLoan = Database::fetch('SELECT * FROM loans WHERE user_id = :uid AND status = :s ORDER BY id DESC LIMIT 1', [
    ':uid' => $userId, ':s' => LOAN_ACTIVE,
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();
    if (!$activeLoan) {
        Session::flash('error', 'You do not have an active loan to pay.');
        Redirect::to('payments');
    }
    $amount = (float) ($_POST['amount'] ?? 0);
    $method = $_POST['payment_method'] ?? '';
    $reference = trim($_POST['reference'] ?? '');

    if ($amount <= 0) {
        Session::flash('error', 'Please enter a valid amount.');
        Redirect::to('payments');
    }
    $result = Loan::processPayment((int) $activeLoan['id'], $amount, $method, $reference);
    if ($result['success']) {
        Session::flash('success', 'Payment of ' . Util::formatMoney($amount) . ' received. Outstanding: ' . Util::formatMoney($result['outstanding']));
        Redirect::to('payments');
    }
    Session::flash('error', $result['message']);
}

$amortization = $activeLoan ? Loan::getAmortization((int) $activeLoan['id']) : [];
$nextDue = null;
foreach ($amortization as $inst) {
    if (in_array($inst['status'], ['pending', 'partial', 'overdue'], true)) {
        $nextDue = $inst;
        break;
    }
}
$paymentMethods = ['cash' => 'Cash', 'bank_transfer' => 'Bank Transfer', 'gcash' => 'GCash', 'maya' => 'Maya', 'over_the_counter' => 'Over the Counter'];
?>

<div class="page-header">
    <?= section_label('Repayment') ?>
    <h1 class="page-title">Payments</h1>
    <p class="page-subtitle">View your schedule and make a payment.</p>
</div>

<?php if (!$activeLoan): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-credit-card" style="font-size:3rem;color:var(--ink-3)"></i>
            <h3 class="mt-3">No active loan</h3>
            <p class="text-muted">You do not have an active loan to make payments on.</p>
            <a href="loan_history.php" class="btn btn-ghost-gold">View loan history</a>
        </div>
    </div>
<?php else: ?>
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Repayment Schedule — <?= htmlspecialchars($activeLoan['loan_reference']) ?></h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>#</th><th>Due Date</th><th>Amount</th><th>Penalty</th><th>Paid</th><th>Status</th></tr></thead>
                            <tbody>
                                <?php foreach ($amortization as $inst): ?>
                                <tr class="<?= $nextDue && $inst['id'] === $nextDue['id'] ? 'table-active' : '' ?>">
                                    <td><?= $inst['installment_number'] ?></td>
                                    <td><?= Util::formatDate($inst['due_date']) ?></td>
                                    <td><?= Util::formatMoney($inst['installment_amount']) ?></td>
                                    <td><?= $inst['penalty_amount'] > 0 ? Util::formatMoney($inst['penalty_amount']) : '—' ?></td>
                                    <td><?= Util::formatMoney($inst['paid_amount']) ?></td>
                                    <td><?= Util::statusBadge($inst['status']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Make a Payment</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Outstanding balance</span>
                            <span class="fw-semibold"><?= Util::formatMoney($activeLoan['outstanding_balance']) ?></span>
                        </div>
                        <?php if ($nextDue): ?>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Next due</span>
                            <span class="fw-semibold"><?= Util::formatDate($nextDue['due_date']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Amount due</span>
                            <span class="fw-semibold"><?= Util::formatMoney((float)$nextDue['installment_amount'] + (float)$nextDue['penalty_amount']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <form method="post" novalidate>
                        <?= Csrf::field() ?>
                        <div class="mb-3">
                            <label class="form-label">Amount (₱)</label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="1" required
                                value="<?= $nextDue ? (float)$nextDue['installment_amount'] + (float)$nextDue['penalty_amount'] : '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment method</label>
                            <select name="payment_method" class="form-select" required>
                                <?php foreach ($paymentMethods as $k => $v): ?>
                                    <option value="<?= $k ?>"><?= $v ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reference no. <small class="text-muted">(optional)</small></label>
                            <input type="text" name="reference" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-emerald w-100" onclick="return confirm('Confirm this payment?')">Pay now</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
