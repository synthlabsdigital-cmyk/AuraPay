<?php
/**
 * Admin Customers Page
 *
 * List, search, view, suspend, and reactivate customer accounts.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Customers';
require_once __DIR__ . '/../includes/admin_header.php';

$search = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? '';
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = ADMIN_PER_PAGE;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();
    $action = $_POST['action'] ?? '';
    $customerId = (int) ($_POST['customer_id'] ?? 0);

    if ($action === 'suspend') {
        Database::update('users', ['status' => USER_STATUS_SUSPENDED], 'id = :id', [':id' => $customerId]);
        ActivityLog::record(type: LOG_STATUS_CHANGE, description: 'Customer suspended: ID ' . $customerId, adminId: Session::userId(), severity: LOG_SEVERITY_WARNING);
        Session::flash('success', 'Customer suspended.');
    } elseif ($action === 'reactivate') {
        Database::update('users', ['status' => USER_STATUS_ACTIVE], 'id = :id', [':id' => $customerId]);
        ActivityLog::record(type: LOG_STATUS_CHANGE, description: 'Customer reactivated: ID ' . $customerId, adminId: Session::userId(), severity: LOG_SEVERITY_INFO);
        Session::flash('success', 'Customer reactivated.');
    }
    Redirect::to('admin_customers');
}

$where = 'WHERE user_type = :t';
$params = [':t' => USER_TYPE_CUSTOMER];
if ($search) {
    $where .= ' AND (first_name LIKE :s OR last_name LIKE :s OR email LIKE :s OR phone LIKE :s)';
    $params[':s'] = "%$search%";
}
if ($statusFilter) {
    $where .= ' AND status = :st';
    $params[':st'] = $statusFilter;
}

$total = Database::count("SELECT COUNT(*) FROM users $where", $params);
$pagination = Util::paginate($total, $perPage, $page);

$sql = "SELECT * FROM users $where ORDER BY created_at DESC LIMIT $perPage OFFSET {$pagination['offset']}";
$customers = Database::fetchAll($sql, $params);
?>

<div class="page-header">
    <?= section_label('Clientele') ?>
    <h1 class="page-title">Customers</h1>
    <p class="page-subtitle">Manage customer accounts.</p>
</div>

<form method="get" class="row g-2 mb-3">
    <div class="col-md-5">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, email, phone..." value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select form-select-sm">
            <option value="">All statuses</option>
            <option value="active" <?= $statusFilter==='active'?'selected':'' ?>>Active</option>
            <option value="pending" <?= $statusFilter==='pending'?'selected':'' ?>>Pending</option>
            <option value="suspended" <?= $statusFilter==='suspended'?'selected':'' ?>>Suspended</option>
            <option value="inactive" <?= $statusFilter==='inactive'?'selected':'' ?>>Inactive</option>
        </select>
    </div>
    <div class="col-md-2"><button type="submit" class="btn btn-ghost-gold btn-sm w-100">Filter</button></div>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($customers as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?></td>
                        <td><?= htmlspecialchars($c['email']) ?></td>
                        <td><?= htmlspecialchars($c['phone']) ?></td>
                        <td><?= Util::statusBadge($c['status']) ?></td>
                        <td><?= Util::formatDate($c['created_at']) ?></td>
                        <td>
                            <a href="customer_detail.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-ghost-gold">View</a>
                            <?php if ($c['status'] === USER_STATUS_ACTIVE): ?>
                            <form method="post" class="d-inline" onsubmit="return confirm('Suspend this customer?')">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="action" value="suspend">
                                <input type="hidden" name="customer_id" value="<?= $c['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-warning">Suspend</button>
                            </form>
                            <?php elseif ($c['status'] === USER_STATUS_SUSPENDED): ?>
                            <form method="post" class="d-inline" onsubmit="return confirm('Reactivate this customer?')">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="action" value="reactivate">
                                <input type="hidden" name="customer_id" value="<?= $c['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-success">Reactivate</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($customers)): ?>
                    <tr><td colspan="6" class="text-muted text-center py-4">No customers found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($pagination['total_pages'] > 1): ?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= !$pagination['has_prev']?'disabled':'' ?>"><a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&status=<?= $statusFilter ?>">Previous</a></li>
        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <li class="page-item <?= $i===$page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= $statusFilter ?>"><?= $i ?></a></li>
        <?php endfor; ?>
        <li class="page-item <?= !$pagination['has_next']?'disabled':'' ?>"><a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&status=<?= $statusFilter ?>">Next</a></li>
    </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
