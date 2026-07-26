<?php
/**
 * OTP Verification Page
 *
 * Verifies the OTP generated during registration or password reset.
 * In development, displays the OTP in a modal popup (no SMS).
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Verify your email';
$authSubtitle = 'Enter the 6-digit verification code we sent to your email.';
require_once __DIR__ . '/../includes/auth_header.php';

$pendingUserId = Session::get('pending_user_id');
if (!$pendingUserId) {
    Session::flash('info', 'Please register or log in first.');
    Redirect::to('register');
}

$purpose = Session::get('pending_otp_purpose', 'registration');
$devOtp = Session::get('pending_otp');
$otpExpires = Session::get('pending_otp_expires');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();

    $action = $_POST['action'] ?? 'verify';

    if ($action === 'resend') {
        $otp = Otp::resend((int) $pendingUserId, $purpose);
        Session::set('pending_otp', $otp['code']);
        Session::set('pending_otp_expires', $otp['expires_at']);
        $devOtp = $otp['code'];
        $otpExpires = $otp['expires_at'];
        Session::flash('info', 'A new OTP has been generated.');
        Redirect::to('verify_otp');
    }

    $code = trim($_POST['code'] ?? '');
    if (strlen($code) !== OTP_LENGTH || !ctype_digit($code)) {
        Session::flash('error', 'Please enter the 6-digit code.');
    } else {
        if ($purpose === 'registration') {
            $result = Auth::verifyEmail((int) $pendingUserId, $code);
            if ($result['success']) {
                Session::remove('pending_user_id');
                Session::remove('pending_otp');
                Session::remove('pending_otp_purpose');
                Session::remove('pending_otp_expires');
                Session::flash('success', 'Your email has been verified. You can now sign in.');
                Redirect::to('login');
            }
        } else {
            $result = Otp::verify((int) $pendingUserId, $code, $purpose);
            if ($result['success']) {
                Session::set('otp_verified_user', $pendingUserId);
                Session::remove('pending_otp');
                Redirect::to('reset');
            }
        }
        Session::flash('error', $result['message'] ?? 'Verification failed.');
    }
}
?>

<form method="post" class="auth-form" novalidate>
    <?= Csrf::field() ?>
    <input type="hidden" name="action" value="verify">
    <div>
        <label>Verification code</label>
        <input type="text" name="code" class="form-control otp-input" maxlength="6" pattern="\d{6}" inputmode="numeric" required autofocus>
    </div>
    <button type="submit" class="btn btn-gold w-100 py-3">Verify</button>
</form>

<div class="auth-links">
    <span>Didn't receive a code?</span>
    <form method="post" class="d-inline">
        <?= Csrf::field() ?>
        <input type="hidden" name="action" value="resend">
        <button type="submit" class="btn btn-link p-0">Resend code</button>
    </form>
</div>

<?php if (Settings::get('dev_otp_enabled', '0') === '1' && $devOtp): ?>
<!-- Development OTP Modal -->
<div class="modal fade" id="devOtpModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--gold-soft); border-bottom: 1px solid var(--line);">
                <h5 class="modal-title" style="color: var(--gold-bright);"><i class="bi bi-info-circle me-2"></i>Development OTP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="text-muted">SMS is disabled. Your verification code is:</p>
                <div class="dev-otp-code my-3"><?= htmlspecialchars($devOtp) ?></div>
                <p class="small text-muted">Expires at <?= Util::formatDateTime($otpExpires) ?></p>
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
