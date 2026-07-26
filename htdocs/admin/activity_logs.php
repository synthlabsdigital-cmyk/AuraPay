<?php
/**
 * Admin Activity Logs Page
 *
 * Audit trail of all admin and system actions with filtering.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Activity Logs';
require_once __DIR__ . '/../includes/admin_header.php';

$typeFilter = $_GET['type'] ?? '';
$severityFilter = $_GET['severity'] ?? '';
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = ADMIN_PER_PAGE;

$logs = ActivityLog::all($perPage, ($page - 1) * $perPage, $typeFilter ?: null, $severityFilter ?: null);
$total = ActivityLog::count($typeFilter ?: null, $severityFilter ?: null);
$pagination = Util::paginate($total, $perPage, $page);

$logTypes = ['login'=>'Login','logout'=>'Logout','create'=>'Create','update'=>'Update','delete'=>'Delete','approve'=>'Approve','reject'=>'Reject','disburse'=>'Disburse','payment'=>'Payment','status_change'=>'Status Change','configuration'=>'Configuration','maintenance'=>'Maintenance','security'=>'Security'];
$severities = ['info'=>'Info','warning'=>'Warning','critical'=>'Critical'];
?>

<div class="page-header">
    <?= section_label('Audit') ?>
    <h1 class="page-title">Activity Logs</h1>
    <p class="page-subtitle">Audit trail of all admin and system actions.</p>
</div>

<form method="get" class="row g-2 mb-3">
    <div class="col-md-4">
        <select name="type" class="form-select form-select-sm">
            <option value="">All types</option>
            <?php foreach ($logTypes as $k=>$v): ?><option value="<?= $k ?>" <?= $typeFilter===$k?'selected':'' ?>><?= $v ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <select name="severity" class="form-select form-select-sm">
            <option value="">All severities</option>
            <?php foreach ($severities as $k=>$v): ?><option value="<?= $k ?>" <?= $severityFilter===$k?'selected':'' ?>><?= $v ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2"><button type="submit" class="btn btn-ghost-gold btn-sm">Filter</button></div>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Time</th><th>Type</th><th>Severity</th><th>Description</th><th>Admin</th><th>IP</th></tr></thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= Util::formatDateTime($log['created_at']) ?></td>
                        <td><span class="badge badge-neutral"><?= htmlspecialchars($log['type']) ?></span></td>
                        <td><span class="badge <?= $log['severity']==='critical'?'badge-rose':($log['severity']==='warning'?'badge-amber':'badge-sky') ?>"><?= ucfirst($log['severity']) ?></span></td>
                        <td><?= htmlspecialchars($log['description']) ?></td>
                        <td><?= htmlspecialchars(($log['admin_first'] ?? 'System') . ' ' . ($log['admin_last'] ?? '')) ?></td>
                        <td><small class="text-muted"><?= htmlspecialchars($log['ip_address']) ?></small></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($logs)): ?><tr><td colspan="6" class="text-muted text-center py-4">No logs found.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($pagination['total_pages'] > 1): ?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= !$pagination['has_prev']?'disabled':'' ?>"><a class="page-link" href="?page=<?= $page-1 ?>&type=<?= $typeFilter ?>&severity=<?= $severityFilter ?>">Previous</a></li>
        <?php for ($i=1;$i<=$pagination['total_pages'];$i++): ?><li class="page-item <?= $i===$page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>&type=<?= $typeFilter ?>&severity=<?= $severityFilter ?>"><?= $i ?></a></li><?php endfor; ?>
        <li class="page-item <?= !$pagination['has_next']?'disabled':'' ?>"><a class="page-link" href="?page=<?= $page+1 ?>&type=<?= $typeFilter ?>&severity=<?= $severityFilter ?>">Next</a></li>
    </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
