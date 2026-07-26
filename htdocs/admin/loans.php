<?php
/**
 * Admin Loans Page
 *
 * Full list of all loans with filtering and search.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Loans';
require_once __DIR__ . '/../includes/admin_header.php';

$search = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? '';
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = ADMIN_PER_PAGE;

$where = 'WHERE 1=1';
$params = [];
if ($search) {
    $where .= ' AND (l.loan_reference LIKE :s OR u.first_name LIKE :s OR u.last_name LIKE :s OR u.email LIKE :s)';
    $params[':s'] = "%$search%";
}
if ($statusFilter) {
    $where .= ' AND l.status = :st';
    $params[':st'] = $statusFilter;
}

$total = Database::count("SELECT COUNT(*) FROM loans l JOIN users u ON l.user_id = u.id $where", $params);
$pagination = Util::paginate($total, $perPage, $page);
$sql = "SELECT l.*, u.first_name, u.last_name, u.email
        FROM loans l JOIN users u ON l.user_id = u.id
        $where ORDER BY l.created_at DESC LIMIT :lim OFFSET :off";
$params[':lim'] = $perPage;
$params[':off'] = $pagination['offset'];
$loans = Database::fetchAll($sql, $params);

$statuses = ['pending'=>'Pending','under_review'=>'Under Review','approved'=>'Approved','rejected'=>'Rejected','active'=>'Active','completed'=>'Completed','defaulted'=>'Defaulted','closed'=>'Closed'];
?>

<div class="page-header">
    <?= section_label('Portfolio') ?>
    <h1 class="page-title">Loans</h1>
    <p class="page-subtitle">All loans across the platform.</p>
</div>

<form method="get" class="row g-2 mb-3">
    <div class="col-md-5"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search reference, customer..." value="<?= htmlspecialchars($search) ?>"></div>
    <div class="col-md-3">
        <select name="status" class="form-select form-select-sm">
            <option value="">All statuses</option>
            <?php foreach ($statuses as $k=>$v): ?><option value="<?= $k ?>" <?= $statusFilter===$k?'selected':'' ?>><?= $v ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2"><button type="submit" class="btn btn-ghost-gold btn-sm">Filter</button></div>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Reference</th><th>Customer</th><th>Principal</th><th>Term</th><th>Monthly</th><th>Outstanding</th><th>Status</th><th>Date</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($loans as $loan): ?>
                    <tr>
                        <td><code><?= htmlspecialchars($loan['loan_reference']) ?></code></td>
                        <td><?= htmlspecialchars($loan['first_name'].' '.$loan['last_name']) ?></td>
                        <td><?= Util::formatMoney($loan['principal_amount']) ?></td>
                        <td><?= $loan['term_months'] ?>mo</td>
                        <td><?= Util::formatMoney($loan['monthly_payment']) ?></td>
                        <td><?= Util::formatMoney($loan['outstanding_balance']) ?></td>
                        <td><?= Util::statusBadge($loan['status']) ?></td>
                        <td><?= Util::formatDate($loan['application_date']) ?></td>
                        <td><a href="loan_detail.php?id=<?= $loan['id'] ?>" class="btn btn-sm btn-ghost-gold">View</a></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($loans)): ?><tr><td colspan="9" class="text-muted text-center py-4">No loans found.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($pagination['total_pages'] > 1): ?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= !$pagination['has_prev']?'disabled':'' ?>"><a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&status=<?= $statusFilter ?>">Previous</a></li>
        <?php for ($i=1;$i<=$pagination['total_pages'];$i++): ?><li class="page-item <?= $i===$page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= $statusFilter ?>"><?= $i ?></a></li><?php endfor; ?>
        <li class="page-item <?= !$pagination['has_next']?'disabled':'' ?>"><a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&status=<?= $statusFilter ?>">Next</a></li>
    </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
