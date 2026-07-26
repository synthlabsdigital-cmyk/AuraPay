<?php
/**
 * Apply for Loan Page
 *
 * Lets the customer apply for a loan. Requires a credit evaluation first.
 * Shows recommended amount/term and calculates the loan summary.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Apply for a Loan';
require_once __DIR__ . '/../includes/header.php';

$userId = Session::userId();
$latestEval = Database::fetch('SELECT * FROM credit_evaluations WHERE user_id = :uid ORDER BY id DESC LIMIT 1', [':uid' => $userId]);
$existing = Database::fetch('SELECT id FROM loans WHERE user_id = :uid AND status IN (:s1,:s2,:s3,:s4)', [
    ':uid' => $userId, ':s1' => LOAN_PENDING, ':s2' => LOAN_UNDER_REVIEW, ':s3' => LOAN_APPROVED, ':s4' => LOAN_ACTIVE,
]);

$product = product();
$loanCfg = $product['loan'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();

    if ($existing) {
        Session::flash('error', 'You already have an active or pending loan.');
        Redirect::to('apply_loan');
    }
    if (!$latestEval || $latestEval['status'] !== CREDIT_COMPLETED) {
        Session::flash('error', 'Please complete a credit evaluation first.');
        Redirect::to('credit');
    }
    if ($latestEval['score'] < $product['credit']['approval_threshold']) {
        Session::flash('error', 'Your credit score does not meet the minimum threshold for a loan.');
        Redirect::to('credit');
    }

    $amount = (float) ($_POST['amount'] ?? 0);
    $term = (int) ($_POST['term_months'] ?? 0);
    $purpose = trim($_POST['purpose'] ?? '');

    $result = Loan::apply($userId, (int) $latestEval['id'], $amount, $term, $purpose);
    if ($result['success']) {
        Session::flash('success', 'Your loan application has been submitted. Reference: ' . $result['reference']);
        Redirect::to('loan_history');
    }
    Session::flash('error', $result['message']);
}

// Loan preview calculation
$previewAmount = isset($_POST['amount']) ? (float) $_POST['amount'] : ($latestEval['recommended_amount'] ?? 0);
$previewTerm = isset($_POST['term_months']) ? (int) $_POST['term_months'] : ($latestEval['recommended_term'] ?? 0);
$preview = null;
if ($previewAmount > 0 && $previewTerm > 0) {
    $rate = (float) $loanCfg['default_interest_rate'];
    $fee = (float) $loanCfg['default_processing_fee'];
    $interest = round($previewAmount * ($rate / 100) * $previewTerm, 2);
    $total = round($previewAmount + $interest + $fee, 2);
    $monthly = round($total / $previewTerm, 2);
    $preview = ['interest' => $interest, 'fee' => $fee, 'total' => $total, 'monthly' => $monthly];
}
?>

<div class="page-header">
    <?= section_label('Application') ?>
    <h1 class="page-title">Apply for a Loan</h1>
    <p class="page-subtitle">Choose your amount and term. All fees shown upfront.</p>
</div>

<?php if ($existing): ?>
    <div class="alert alert-warning">
        <i class="bi bi-info-circle me-1"></i>You already have an active or pending loan. <a href="loan_history.php">View your loans</a>.
    </div>
<?php elseif (!$latestEval): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-1"></i>You need a credit evaluation before applying. <a href="credit_evaluation.php">Run evaluation</a>.
    </div>
<?php elseif ($latestEval['score'] < $product['credit']['approval_threshold']): ?>
    <div class="alert alert-danger">
        <i class="bi bi-x-circle me-1"></i>Your credit score (<?= $latestEval['score'] ?>) is below the approval threshold (<?= $product['credit']['approval_threshold'] ?>). Please improve your profile and re-evaluate.
    </div>
<?php else: ?>
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Loan Application</h5></div>
                <div class="card-body">
                    <form method="post" id="loanForm" novalidate>
                        <?= Csrf::field() ?>
                        <div class="mb-3">
                            <label class="form-label">Loan amount (₱)</label>
                            <input type="number" name="amount" id="amount" class="form-control" step="1000"
                                   min="<?= $loanCfg['min_amount'] ?>" max="<?= $loanCfg['max_amount'] ?>"
                                   value="<?= $previewAmount ?>" required>
                            <small class="text-muted">Min ₱<?= number_format($loanCfg['min_amount']) ?>, max ₱<?= number_format($loanCfg['max_amount']) ?></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Term (months)</label>
                            <select name="term_months" id="term_months" class="form-select" required>
                                <?php for ($t = $loanCfg['min_term']; $t <= $loanCfg['max_term']; $t++): ?>
                                    <option value="<?= $t ?>" <?= $previewTerm === $t ? 'selected' : '' ?>><?= $t ?> months</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Purpose <small class="text-muted">(optional)</small></label>
                            <textarea name="purpose" class="form-control" rows="2"><?= htmlspecialchars($_POST['purpose'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-gold" onclick="return confirm('Submit this loan application?')">
                            Submit application
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Loan Summary</h5></div>
                <div class="card-body" id="loanSummary">
                    <?php if ($preview): ?>
                        <div class="row g-2">
                            <div class="col-7 text-muted">Principal amount</div>
                            <div class="col-5 text-end fw-semibold"><?= Util::formatMoney($previewAmount) ?></div>
                            <div class="col-7 text-muted">Interest (<?= $loanCfg['default_interest_rate'] ?>%/mo &times; <?= $previewTerm ?>mo)</div>
                            <div class="col-5 text-end"><?= Util::formatMoney($preview['interest']) ?></div>
                            <div class="col-7 text-muted">Processing fee</div>
                            <div class="col-5 text-end"><?= Util::formatMoney($preview['fee']) ?></div>
                            <hr class="my-2 gold-divider">
                            <div class="col-7 fw-semibold">Total payable</div>
                            <div class="col-5 text-end fw-semibold"><?= Util::formatMoney($preview['total']) ?></div>
                            <div class="col-7 fw-semibold text-primary" style="color: var(--gold-bright) !important;">Monthly payment</div>
                            <div class="col-5 text-end fw-semibold fs-5 gold-text"><?= Util::formatMoney($preview['monthly']) ?></div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Enter an amount and term to see the summary.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <div class="small text-muted">Based on your credit evaluation</div>
                    <div class="d-flex justify-content-between mt-2">
                        <span>Recommended amount</span>
                        <span class="fw-semibold"><?= $latestEval['recommended_amount'] > 0 ? Util::formatMoney($latestEval['recommended_amount']) : '—' ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Recommended term</span>
                        <span class="fw-semibold"><?= $latestEval['recommended_term'] > 0 ? $latestEval['recommended_term'] . ' months' : '—' ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function() {
        const amount = document.getElementById('amount');
        const term = document.getElementById('term_months');
        const summary = document.getElementById('loanSummary');
        async function update() {
            const a = parseFloat(amount.value) || 0;
            const t = parseInt(term.value) || 0;
            if (a <= 0 || t <= 0) return;
            const rate = <?= $loanCfg['default_interest_rate'] ?>;
            const fee = <?= $loanCfg['default_processing_fee'] ?>;
            const interest = Math.round(a * (rate/100) * t * 100) / 100;
            const total = Math.round((a + interest + fee) * 100) / 100;
            const monthly = Math.round(total / t * 100) / 100;
            summary.innerHTML = `
                <div class="row g-2">
                    <div class="col-7 text-muted">Principal amount</div>
                    <div class="col-5 text-end fw-semibold">₱\${a.toLocaleString('en-PH',{minimumFractionDigits:2})}</div>
                    <div class="col-7 text-muted">Interest (\${rate}%/mo &times; \${t}mo)</div>
                    <div class="col-5 text-end">₱\${interest.toLocaleString('en-PH',{minimumFractionDigits:2})}</div>
                    <div class="col-7 text-muted">Processing fee</div>
                    <div class="col-5 text-end">₱\${fee.toLocaleString('en-PH',{minimumFractionDigits:2})}</div>
                    <hr class="my-2 gold-divider">
                    <div class="col-7 fw-semibold">Total payable</div>
                    <div class="col-5 text-end fw-semibold">₱\${total.toLocaleString('en-PH',{minimumFractionDigits:2})}</div>
                    <div class="col-7 fw-semibold text-primary" style="color: var(--gold-bright) !important;">Monthly payment</div>
                    <div class="col-5 text-end fw-semibold text-primary fs-5 gold-text">₱\${monthly.toLocaleString('en-PH',{minimumFractionDigits:2})}</div>
                </div>`;
        }
        amount.addEventListener('input', update);
        term.addEventListener('change', update);
    })();
    </script>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
