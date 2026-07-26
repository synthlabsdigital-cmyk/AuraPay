<?php
/**
 * Loan History Page
 *
 * Lists all of the customer's loans with status and key details.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Loan History';
require_once __DIR__ . '/../includes/header.php';

$userId = Session::userId();
$loans = Database::fetchAll('SELECT * FROM loans WHERE user_id = :uid ORDER BY created_at DESC', [':uid' => $userId]);
?>

<div class="page-header">
    <?= section_label('History') ?>
    <h1 class="page-title">Loan History</h1>
    <p class="page-subtitle">All your loan applications and their status.</p>
</div>

<?php if (empty($loans)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-clock-history" style="font-size:3rem;color:var(--text-muted)"></i>
            <h3 class="mt-3">No loans yet</h3>
            <p class="text-muted">When you apply for a loan, it will appear here.</p>
            <a href="apply_loan.php" class="btn btn-gold">Apply for a loan</a>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Amount</th>
                            <th>Term</th>
                            <th>Monthly</th>
                            <th>Outstanding</th>
                            <th>Status</th>
                            <th>Applied</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($loans as $loan): ?>
                        <tr>
                            <td><code><?= htmlspecialchars($loan['loan_reference']) ?></code></td>
                            <td><?= Util::formatMoney($loan['principal_amount']) ?></td>
                            <td><?= $loan['term_months'] ?> mo</td>
                            <td><?= Util::formatMoney($loan['monthly_payment']) ?></td>
                            <td><?= Util::formatMoney($loan['outstanding_balance']) ?></td>
                            <td><?= Util::statusBadge($loan['status']) ?></td>
                            <td><?= Util::formatDate($loan['application_date']) ?></td>
                            <td><a href="loan_detail.php?id=<?= $loan['id'] ?>" class="btn btn-sm btn-ghost-gold">View</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
