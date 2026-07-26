<?php
/**
 * Notifications Page
 *
 * Lists all notifications and allows marking them as read.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Notifications';
require_once __DIR__ . '/../includes/header.php';

$userId = Session::userId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();
    $action = $_POST['action'] ?? '';
    if ($action === 'mark_all') {
        Notification::markAllRead($userId);
        Session::flash('success', 'All notifications marked as read.');
    } elseif ($action === 'mark_one') {
        Notification::markRead((int) $_POST['notif_id'], $userId);
    }
    Redirect::to('notifications');
}

$notifications = Database::fetchAll('SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC', [':uid' => $userId]);
$routes = require CONFIG_PATH . '/routes.php';
$iconMap = [
    'info' => 'info-circle text-info',
    'success' => 'check-circle text-success',
    'warning' => 'exclamation-triangle text-warning',
    'error' => 'x-circle text-danger',
    'loan' => 'cash-coin text-primary',
    'payment' => 'credit-card text-primary',
    'document' => 'file-earmark text-info',
    'system' => 'gear text-secondary',
];
?>

<div class="page-header d-flex justify-content-between align-items-end">
    <div>
        <?= section_label('Inbox') ?>
        <h1 class="page-title">Notifications</h1>
        <p class="page-subtitle">Stay updated on your account activity.</p>
    </div>
    <?php if (!empty($notifications)): ?>
    <form method="post">
        <?= Csrf::field() ?>
        <input type="hidden" name="action" value="mark_all">
        <button type="submit" class="btn btn-ghost-gold btn-sm">Mark all read</button>
    </form>
    <?php endif; ?>
</div>

<?php if (empty($notifications)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-bell-slash" style="font-size:3rem;color:var(--ink-3)"></i>
            <h3 class="mt-3">No notifications</h3>
            <p class="text-muted">You are all caught up.</p>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="list-group list-group-flush">
            <?php foreach ($notifications as $n): ?>
            <div class="list-group-item <?= $n['is_read'] ? '' : 'fw-semibold' ?>" style="background: <?= $n['is_read'] ? 'transparent' : 'var(--gold-soft)' ?>; border-color: var(--line-soft);">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-<?= explode(' ', $iconMap[$n['type']] ?? 'info-circle text-info')[0] ?> <?= explode(' ', $iconMap[$n['type']] ?? 'info-circle text-info')[1] ?? '' ?> fs-5 mt-1"></i>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <span><?= htmlspecialchars($n['title']) ?></span>
                            <small class="text-muted"><?= Util::timeAgo($n['created_at']) ?></small>
                        </div>
                        <p class="mb-0 small text-muted <?= $n['is_read'] ? '' : 'fw-normal' ?>"><?= htmlspecialchars($n['message']) ?></p>
                        <?php if ($n['link']): ?>
                            <?php $linkPath = $routes[$n['link']] ?? $n['link']; ?>
                            <a href="<?= BASE_PATH . '/' . ltrim($linkPath, '/') ?>" class="small">View &rarr;</a>
                        <?php endif; ?>
                    </div>
                    <?php if (!$n['is_read']): ?>
                    <form method="post" class="ms-2">
                        <?= Csrf::field() ?>
                        <input type="hidden" name="action" value="mark_one">
                        <input type="hidden" name="notif_id" value="<?= $n['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-link p-0" title="Mark as read"><i class="bi bi-check2"></i></button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
