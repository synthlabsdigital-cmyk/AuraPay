<?php
/**
 * Reset Password Page
 *
 * Allows the user to set a new password after OTP verification.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Reset password';
$authSubtitle = 'Enter the verification code and your new password.';
require_once __DIR__ . '/../includes/auth_header.php';

$userId = Session::get('otp_verified_user');
if (!$userId) {
    Session::flash('info', 'Please verify your email first.');
    Redirect::to('forgot');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();
    $code = trim($_POST['code'] ?? '');
    $newPassword = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($newPassword !== $confirm) {
        Session::flash('error', 'Passwords do not match.');
    } else {
        $result = Auth::resetPassword((int) $userId, $code, $newPassword);
        if ($result['success']) {
            Session::remove('otp_verified_user');
            Session::remove('pending_user_id');
            Session::remove('pending_otp');
            Session::remove('pending_otp_purpose');
            Session::flash('success', 'Your password has been reset. You can now sign in.');
            Redirect::to('login');
        }
        Session::flash('error', $result['message']);
    }
}

$devOtp = Session::get('pending_otp');
?>

<form method="post" class="auth-form" novalidate>
    <?= Csrf::field() ?>
    <div>
        <label>Verification code</label>
        <input type="text" name="code" class="form-control otp-input" maxlength="6" pattern="\d{6}" inputmode="numeric" required autofocus>
    </div>
    <div>
        <label>New password</label>
        <input type="password" name="password" class="form-control" required minlength="8">
        <small class="text-muted">Min 8 chars, letters and numbers.</small>
    </div>
    <div>
        <label>Confirm new password</label>
        <input type="password" name="confirm_password" class="form-control" required minlength="8">
    </div>
    <button type="submit" class="btn btn-gold w-100 py-3">Reset password</button>
</form>

<?php if (Settings::get('dev_otp_enabled', '0') === '1' && $devOtp): ?>
<div class="modal fade" id="devOtpModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--gold-soft); border-bottom: 1px solid var(--line);">
                <h5 class="modal-title" style="color: var(--gold-bright);"><i class="bi bi-info-circle me-2"></i>Development OTP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="text-muted">SMS is disabled. Your reset code is:</p>
                <div class="dev-otp-code my-3"><?= htmlspecialchars($devOtp) ?></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-gold" data-bs-dismiss="modal">Got it</button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new bootstrap.Modal(document.getElementById('devOtpModal')).show();
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/auth_footer.php'; ?>
