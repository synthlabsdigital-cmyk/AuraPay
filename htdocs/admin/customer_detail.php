<?php
/**
 * Admin Customer Detail Page
 *
 * Full customer profile, documents, credit evaluations, loans, and transactions.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Customer Details';
require_once __DIR__ . '/../includes/admin_header.php';

$customerId = (int) ($_GET['id'] ?? 0);
$customer = Database::fetch('SELECT * FROM users WHERE id = :id AND user_type = :t', [':id' => $customerId, ':t' => USER_TYPE_CUSTOMER]);
if (!$customer) {
    Session::flash('error', 'Customer not found.');
    Redirect::to('admin_customers');
}

$profile = Database::fetch('SELECT * FROM user_profiles WHERE user_id = :uid', [':uid' => $customerId]);
$docs = Database::fetchAll('SELECT * FROM documents WHERE user_id = :uid ORDER BY created_at DESC', [':uid' => $customerId]);
$evals = Database::fetchAll('SELECT * FROM credit_evaluations WHERE user_id = :uid ORDER BY created_at DESC', [':uid' => $customerId]);
$loans = Database::fetchAll('SELECT * FROM loans WHERE user_id = :uid ORDER BY created_at DESC', [':uid' => $customerId]);
$txs = Database::fetchAll('SELECT * FROM transactions WHERE user_id = :uid ORDER BY transaction_date DESC LIMIT 10', [':uid' => $customerId]);
?>

<div class="page-header">
    <?= section_label('Profile') ?>
    <h1 class="page-title"><?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></h1>
    <p class="page-subtitle"><?= htmlspecialchars($customer['email']) ?> &middot; <?= Util::statusBadge($customer['status']) ?></p>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">Profile</h5></div>
            <div class="card-body small">
                <div class="mb-1"><span class="text-muted">Phone:</span> <?= htmlspecialchars($customer['phone']) ?></div>
                <div class="mb-1"><span class="text-muted">Joined:</span> <?= Util::formatDate($customer['created_at']) ?></div>
                <div class="mb-1"><span class="text-muted">Last login:</span> <?= Util::formatDateTime($customer['last_login_at']) ?></div>
                <div class="mb-1"><span class="text-muted">DOB:</span> <?= Util::formatDate($profile['date_of_birth'] ?? null) ?></div>
                <div class="mb-1"><span class="text-muted">Employment:</span> <?= ucfirst(str_replace('_',' ',$profile['employment_status'] ?? '')) ?></div>
                <div class="mb-1"><span class="text-muted">Monthly income:</span> <?= $profile['monthly_income'] ? Util::formatMoney($profile['monthly_income']) : '—' ?></div>
                <div class="mb-1"><span class="text-muted">Address:</span> <?= htmlspecialchars($profile['city'] ?? '') ?>, <?= htmlspecialchars($profile['province'] ?? '') ?></div>
                <div class="mb-1"><span class="text-muted">ID type:</span> <?= htmlspecialchars($profile['id_type'] ?? '—') ?></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">Documents</h5></div>
            <div class="card-body">
                <?php if (empty($docs)): ?>
                    <p class="text-muted small mb-0">No documents uploaded.</p>
                <?php else: foreach ($docs as $doc): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <div class="small"><?= ucfirst(str_replace('_',' ',$doc['document_type'])) ?></div>
                            <div class="text-muted" style="font-size:.75rem"><?= Util::formatDate($doc['created_at']) ?></div>
                        </div>
                        <?= Util::statusBadge($doc['status']) ?>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">Credit Evaluations</h5></div>
            <div class="card-body p-0">
                <?php if (empty($evals)): ?>
                    <p class="text-muted text-center py-3 mb-0">No evaluations.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Score</th><th>Rating</th><th>Risk</th><th>Recommended</th><th>Date</th></tr></thead>
                            <tbody>
                                <?php foreach ($evals as $ev): ?>
                                <tr>
                                    <td><?= $ev['score'] ?></td>
                                    <td><?= htmlspecialchars($ev['rating']) ?></td>
                                    <td><?= ucfirst($ev['risk_level']) ?></td>
                                    <td><?= Util::formatMoney($ev['recommended_amount']) ?> / <?= $ev['recommended_term'] ?>mo</td>
                                    <td><?= Util::formatDate($ev['created_at']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">Loans</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Reference</th><th>Amount</th><th>Term</th><th>Status</th><th>Outstanding</th><th>Date</th></tr></thead>
                        <tbody>
                            <?php foreach ($loans as $loan): ?>
                            <tr>
                                <td><code><?= htmlspecialchars($loan['loan_reference']) ?></code></td>
                                <td><?= Util::formatMoney($loan['principal_amount']) ?></td>
                                <td><?= $loan['term_months'] ?>mo</td>
                                <td><?= Util::statusBadge($loan['status']) ?></td>
                                <td><?= Util::formatMoney($loan['outstanding_balance']) ?></td>
                                <td><?= Util::formatDate($loan['application_date']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($loans)): ?>
                            <tr><td colspan="6" class="text-muted text-center py-3">No loans.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">Recent Transactions</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Reference</th><th>Type</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                        <tbody>
                            <?php foreach ($txs as $tx): ?>
                            <tr>
                                <td><code><?= htmlspecialchars($tx['transaction_reference']) ?></code></td>
                                <td><?= ucfirst($tx['type']) ?></td>
                                <td><?= Util::formatMoney($tx['amount']) ?></td>
                                <td><?= Util::statusBadge($tx['status']) ?></td>
                                <td><?= Util::formatDate($tx['transaction_date']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($txs)): ?>
                            <tr><td colspan="5" class="text-muted text-center py-3">No transactions.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
