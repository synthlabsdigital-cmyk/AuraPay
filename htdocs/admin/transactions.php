<?php
/**
 * Admin Transactions Page
 *
 * All transactions across the platform with filtering.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Transactions';
require_once __DIR__ . '/../includes/admin_header.php';

$typeFilter = $_GET['type'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = ADMIN_PER_PAGE;

$where = 'WHERE 1=1';
$params = [];
if ($typeFilter) { $where .= ' AND t.type = :t'; $params[':t'] = $typeFilter; }
if ($statusFilter) { $where .= ' AND t.status = :s'; $params[':s'] = $statusFilter; }
if ($search) {
    $where .= ' AND (t.transaction_reference LIKE :q OR u.first_name LIKE :q OR u.last_name LIKE :q OR u.email LIKE :q)';
    $params[':q'] = "%$search%";
}

$total = Database::count("SELECT COUNT(*) FROM transactions t JOIN users u ON t.user_id = u.id $where", $params);
$pagination = Util::paginate($total, $perPage, $page);
$sql = "SELECT t.*, u.first_name, u.last_name, u.email
        FROM transactions t JOIN users u ON t.user_id = u.id
        $where ORDER BY t.transaction_date DESC LIMIT $perPage OFFSET {$pagination['offset']}";
$transactions = Database::fetchAll($sql, $params);
$types = ['disbursement'=>'Disbursement','repayment'=>'Repayment','fee'=>'Fee','interest'=>'Interest','penalty'=>'Penalty','adjustment'=>'Adjustment'];
?>

<div class="page-header">
    <?= section_label('Ledger') ?>
    <h1 class="page-title">Transactions</h1>
    <p class="page-subtitle">All platform transactions.</p>
</div>

<form method="get" class="row g-2 mb-3">
    <div class="col-md-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search reference, customer..." value="<?= htmlspecialchars($search) ?>"></div>
    <div class="col-md-3">
        <select name="type" class="form-select form-select-sm">
            <option value="">All types</option>
            <?php foreach ($types as $k=>$v): ?><option value="<?= $k ?>" <?= $typeFilter===$k?'selected':'' ?>><?= $v ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select form-select-sm">
            <option value="">All statuses</option>
            <option value="completed" <?= $statusFilter==='completed'?'selected':'' ?>>Completed</option>
            <option value="pending" <?= $statusFilter==='pending'?'selected':'' ?>>Pending</option>
            <option value="failed" <?= $statusFilter==='failed'?'selected':'' ?>>Failed</option>
        </select>
    </div>
    <div class="col-md-2"><button type="submit" class="btn btn-ghost-gold btn-sm">Filter</button></div>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Reference</th><th>Customer</th><th>Type</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                    <?php foreach ($transactions as $tx): ?>
                    <tr>
                        <td><code><?= htmlspecialchars($tx['transaction_reference']) ?></code></td>
                        <td><?= htmlspecialchars($tx['first_name'].' '.$tx['last_name']) ?></td>
                        <td><?= ucfirst($tx['type']) ?></td>
                        <td><?= Util::formatMoney($tx['amount']) ?></td>
                        <td><?= ucfirst(str_replace('_',' ',$tx['payment_method'] ?? '')) ?></td>
                        <td><?= Util::statusBadge($tx['status']) ?></td>
                        <td><?= Util::formatDateTime($tx['transaction_date']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($transactions)): ?><tr><td colspan="7" class="text-muted text-center py-4">No transactions found.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($pagination['total_pages'] > 1): ?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= !$pagination['has_prev']?'disabled':'' ?>"><a class="page-link" href="?page=<?= $page-1 ?>&type=<?= $typeFilter ?>&status=<?= $statusFilter ?>&search=<?= urlencode($search) ?>">Previous</a></li>
        <?php for ($i=1;$i<=$pagination['total_pages'];$i++): ?><li class="page-item <?= $i===$page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>&type=<?= $typeFilter ?>&status=<?= $statusFilter ?>&search=<?= urlencode($search) ?>"><?= $i ?></a></li><?php endfor; ?>
        <li class="page-item <?= !$pagination['has_next']?'disabled':'' ?>"><a class="page-link" href="?page=<?= $page+1 ?>&type=<?= $typeFilter ?>&status=<?= $statusFilter ?>&search=<?= urlencode($search) ?>">Next</a></li>
    </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
