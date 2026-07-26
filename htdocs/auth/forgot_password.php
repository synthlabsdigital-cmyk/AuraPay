<?php
/**
 * Forgot Password Page
 *
 * Initiates password reset by generating an OTP. In development, the OTP
 * is shown in a modal (no SMS).
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Forgot password';
$authSubtitle = 'Enter your email and we will send you a reset code.';
require_once __DIR__ . '/../includes/auth_header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();
    $email = trim($_POST['email'] ?? '');

    $result = Auth::initiatePasswordReset($email);

    if (!empty($result['user_id'])) {
        Session::set('pending_user_id', $result['user_id']);
        Session::set('pending_otp', $result['otp']['code'] ?? null);
        Session::set('pending_otp_purpose', 'password_reset');
        Session::set('pending_otp_expires', $result['otp']['expires_at'] ?? null);
        Redirect::to('verify_otp');
    }

    Session::flash('info', 'If an account exists for that email, a reset code has been sent.');
}
?>

<form method="post" class="auth-form" novalidate>
    <?= Csrf::field() ?>
    <div>
        <label>Email address</label>
        <input type="email" name="email" class="form-control" required autofocus>
    </div>
    <button type="submit" class="btn btn-gold w-100 py-3">Send reset code</button>
</form>

<div class="auth-links">
    <a href="login.php">&larr; Back to sign in</a>
</div>

<?php require_once __DIR__ . '/../includes/auth_footer.php'; ?>
