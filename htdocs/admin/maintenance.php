<?php
/**
 * Admin Maintenance Page
 *
 * Toggle maintenance mode and manage maintenance messages.
 * Restricted to super_admin and admin roles.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Maintenance';
require_once __DIR__ . '/../includes/admin_header.php';

Session::requireRole(ROLE_SUPER_ADMIN, ROLE_ADMIN);

$adminId = Session::userId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle') {
        $current = Settings::get('app_status', 'active');
        $new = $current === 'maintenance' ? 'active' : 'maintenance';
        Settings::set('app_status', $new, $adminId);
        ActivityLog::record(type: LOG_MAINTENANCE, description: 'Maintenance mode ' . ($new === 'maintenance' ? 'enabled' : 'disabled'), adminId: $adminId, severity: $new === 'maintenance' ? LOG_SEVERITY_CRITICAL : LOG_SEVERITY_INFO);
        Session::flash('success', 'Maintenance mode ' . ($new === 'maintenance' ? 'enabled' : 'disabled') . '.');
    } elseif ($action === 'create_message') {
        Database::insert('maintenance_messages', [
            'title' => trim($_POST['title'] ?? ''),
            'message' => trim($_POST['message'] ?? ''),
            'start_at' => $_POST['start_at'] ?: null,
            'end_at' => $_POST['end_at'] ?: null,
            'is_active' => 1,
            'created_by' => $adminId,
        ]);
        ActivityLog::record(type: LOG_MAINTENANCE, description: 'Maintenance message created', adminId: $adminId, severity: LOG_SEVERITY_INFO);
        Session::flash('success', 'Maintenance message created.');
    } elseif ($action === 'delete_message') {
        Database::delete('maintenance_messages', 'id = :id', [':id' => (int) $_POST['msg_id']]);
        Session::flash('success', 'Message deleted.');
    } elseif ($action === 'apply_penalties') {
        $count = Loan::applyPenalties();
        ActivityLog::record(type: LOG_MAINTENANCE, description: "Applied late penalties to $count installments", adminId: $adminId, severity: LOG_SEVERITY_WARNING);
        Session::flash('success', "Applied penalties to $count overdue installments.");
    }
    Redirect::to('admin_maintenance');
}

$appStatus = Settings::get('app_status', 'active');
$messages = Database::fetchAll('SELECT * FROM maintenance_messages ORDER BY created_at DESC');
?>

<div class="page-header">
    <?= section_label('Operations') ?>
    <h1 class="page-title">Maintenance</h1>
    <p class="page-subtitle">Control platform availability and messages.</p>
</div>

<!-- Status -->
<div class="card mb-3">
    <div class="card-header"><h5 class="mb-0">Application Status</h5></div>
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <span class="badge <?= $appStatus==='active'?'badge-emerald':'badge-rose' ?> fs-6">
                    <?= $appStatus === 'active' ? 'Active' : 'Maintenance Mode' ?>
                </span>
                <p class="text-muted small mt-2 mb-0">
                    <?= $appStatus === 'active' ? 'The application is running normally.' : 'The application is in maintenance mode. Customers cannot access the portal.' ?>
                </p>
            </div>
            <form method="post" onsubmit="return confirm('Toggle maintenance mode?')">
                <?= Csrf::field() ?>
                <input type="hidden" name="action" value="toggle">
                <button type="submit" class="btn <?= $appStatus==='active'?'btn-ghost-gold':'btn-emerald' ?>" style="<?= $appStatus==='active'?'color: var(--amber); border-color: rgba(251,191,36,0.3);':'' ?>">
                    <?= $appStatus === 'active' ? 'Enable Maintenance' : 'Disable Maintenance' ?>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Penalties -->
<div class="card mb-3">
    <div class="card-header"><h5 class="mb-0">Late Payment Penalties</h5></div>
    <div class="card-body">
        <p class="text-muted small">Manually apply late payment penalties to all overdue installments.</p>
        <form method="post" onsubmit="return confirm('Apply penalties to all overdue installments?')">
            <?= Csrf::field() ?>
            <input type="hidden" name="action" value="apply_penalties">
            <button type="submit" class="btn btn-ghost-gold" style="color: var(--amber); border-color: rgba(251,191,36,0.3);">Apply penalties now</button>
        </form>
    </div>
</div>

<!-- Create message -->
<div class="card mb-3">
    <div class="card-header"><h5 class="mb-0">Create Maintenance Message</h5></div>
    <div class="card-body">
        <form method="post">
            <?= Csrf::field() ?>
            <input type="hidden" name="action" value="create_message">
            <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Message</label><textarea name="message" class="form-control" rows="3" required></textarea></div>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Start at (optional)</label><input type="datetime-local" name="start_at" class="form-control"></div>
                <div class="col-md-4"><label class="form-label">End at (optional)</label><input type="datetime-local" name="end_at" class="form-control"></div>
            </div>
            <button type="submit" class="btn btn-gold mt-3">Create message</button>
        </form>
    </div>
</div>

<!-- Existing messages -->
<div class="card">
    <div class="card-header"><h5 class="mb-0">Existing Messages</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr><th>Title</th><th>Message</th><th>Active</th><th>Created</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($messages as $msg): ?>
                    <tr>
                        <td><?= htmlspecialchars($msg['title']) ?></td>
                        <td class="small"><?= htmlspecialchars($msg['message']) ?></td>
                        <td><?= $msg['is_active'] ? '<span class="badge badge-emerald">Yes</span>' : '<span class="badge badge-neutral">No</span>' ?></td>
                        <td><?= Util::formatDate($msg['created_at']) ?></td>
                        <td>
                            <form method="post" class="d-inline" onsubmit="return confirm('Delete this message?')">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="action" value="delete_message">
                                <input type="hidden" name="msg_id" value="<?= $msg['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($messages)): ?><tr><td colspan="5" class="text-muted text-center py-3">No messages.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
