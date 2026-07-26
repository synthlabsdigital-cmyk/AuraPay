<?php
/**
 * Transactions Page
 *
 * Full transaction history for the customer with filtering and pagination.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Transactions';
require_once __DIR__ . '/../includes/header.php';

$userId = Session::userId();
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = DEFAULT_PER_PAGE;
$typeFilter = $_GET['type'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$where = 'WHERE user_id = :uid';
$params = [':uid' => $userId];
if ($typeFilter) { $where .= ' AND type = :t'; $params[':t'] = $typeFilter; }
if ($statusFilter) { $where .= ' AND status = :s'; $params[':s'] = $statusFilter; }

$total = Database::count("SELECT COUNT(*) FROM transactions $where", $params);
$pagination = Util::paginate($total, $perPage, $page);

$sql = "SELECT * FROM transactions $where ORDER BY transaction_date DESC LIMIT :lim OFFSET :off";
$params[':lim'] = $perPage;
$params[':off'] = $pagination['offset'];
$transactions = Database::fetchAll($sql, $params);
$types = ['disbursement' => 'Disbursement', 'repayment' => 'Repayment', 'fee' => 'Fee', 'interest' => 'Interest', 'penalty' => 'Penalty', 'adjustment' => 'Adjustment'];
$statuses = ['pending' => 'Pending', 'completed' => 'Completed', 'failed' => 'Failed'];
?>

<div class="page-header">
    <?= section_label('Activity') ?>
    <h1 class="page-title">Transactions</h1>
    <p class="page-subtitle">Your complete transaction history.</p>
</div>

<!-- Filters -->
<form method="get" class="row g-2 mb-3 align-items-end">
    <div class="col-md-3">
        <label class="form-label small">Type</label>
        <select name="type" class="form-select form-select-sm">
            <option value="">All</option>
            <?php foreach ($types as $k => $v): ?>
                <option value="<?= $k ?>" <?= $typeFilter === $k ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label small">Status</label>
        <select name="status" class="form-select form-select-sm">
            <option value="">All</option>
            <?php foreach ($statuses as $k => $v): ?>
                <option value="<?= $k ?>" <?= $statusFilter === $k ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-ghost-gold btn-sm w-100">Filter</button>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($transactions)): ?>
            <p class="text-muted text-center py-4 mb-0">No transactions found.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Reference</th><th>Type</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr>
                    </thead>
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

<?php if ($pagination['total_pages'] > 1): ?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= !$pagination['has_prev'] ? 'disabled' : '' ?>"><a class="page-link" href="?page=<?= $page-1 ?>&type=<?= $typeFilter ?>&status=<?= $statusFilter ?>">Previous</a></li>
        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>&type=<?= $typeFilter ?>&status=<?= $statusFilter ?>"><?= $i ?></a></li>
        <?php endfor; ?>
        <li class="page-item <?= !$pagination['has_next'] ? 'disabled' : '' ?>"><a class="page-link" href="?page=<?= $page+1 ?>&type=<?= $typeFilter ?>&status=<?= $statusFilter ?>">Next</a></li>
    </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
