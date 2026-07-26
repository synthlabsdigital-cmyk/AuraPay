<?php
/**
 * Settings Page
 *
 * Change password and manage account preferences.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Settings';
require_once __DIR__ . '/../includes/header.php';

$userId = Session::userId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();
    $action = $_POST['action'] ?? 'password';

    if ($action === 'password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($new !== $confirm) {
            Session::flash('error', 'New passwords do not match.');
        } else {
            $result = Auth::changePassword($userId, $current, $new);
            if ($result['success']) {
                Session::flash('success', 'Your password has been changed.');
                Redirect::to('settings');
            }
            Session::flash('error', $result['message']);
        }
    }
}

$user = Database::fetch('SELECT * FROM users WHERE id = :id', [':id' => $userId]);
?>

<div class="page-header">
    <?= section_label('Security') ?>
    <h1 class="page-title">Settings</h1>
    <p class="page-subtitle">Manage your account security.</p>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Change Password</h5></div>
            <div class="card-body">
                <form method="post" novalidate>
                    <?= Csrf::field() ?>
                    <input type="hidden" name="action" value="password">
                    <div class="mb-3">
                        <label class="form-label">Current password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New password</label>
                        <input type="password" name="new_password" class="form-control" required minlength="8">
                        <small class="text-muted">Min 8 chars, letters and numbers.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm new password</label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="8">
                    </div>
                    <button type="submit" class="btn btn-gold">Update password</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Account Information</h5></div>
            <div class="card-body">
                <div class="mb-2">
                    <span class="text-muted small">Name</span>
                    <div><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                </div>
                <div class="mb-2">
                    <span class="text-muted small">Email</span>
                    <div><?= htmlspecialchars($user['email']) ?></div>
                </div>
                <div class="mb-2">
                    <span class="text-muted small">Phone</span>
                    <div><?= htmlspecialchars($user['phone']) ?></div>
                </div>
                <div class="mb-2">
                    <span class="text-muted small">Member since</span>
                    <div><?= Util::formatDate($user['created_at']) ?></div>
                </div>
                <div class="mb-2">
                    <span class="text-muted small">Last login</span>
                    <div><?= Util::formatDateTime($user['last_login_at']) ?></div>
                </div>
                <div class="mb-2">
                    <span class="text-muted small">Status</span>
                    <div><?= Util::statusBadge($user['status']) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
