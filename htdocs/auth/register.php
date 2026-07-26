<?php
/**
 * Registration Page
 *
 * Customer registration with email, name, phone, password. On submit,
 * creates the account (pending) and redirects to OTP verification.
 * In development, the OTP is displayed in a modal (no SMS).
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/product.php';
$pageTitle = 'Create your account';
$authSubtitle = 'Join ' . htmlspecialchars(product()['name']) . ' and apply for a loan in minutes.';
require_once __DIR__ . '/../includes/auth_header.php';

if (Session::isLoggedIn()) {
    Redirect::to(Session::isCustomer() ? 'dashboard' : 'admin_dashboard');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();

    $result = Auth::register($_POST);

    if ($result['success']) {
        Session::set('pending_user_id', $result['user_id']);
        Session::set('pending_otp', $result['otp']['code']);
        Session::set('pending_otp_purpose', 'registration');
        Session::set('pending_otp_expires', $result['otp']['expires_at']);
        Session::flash('info', 'Your account has been created. Please verify your email with the OTP sent to you.');
        Redirect::to('verify_otp');
    } else {
        Session::flash('error', $result['message']);
    }
}
?>

<form method="post" class="auth-form" novalidate>
    <?= Csrf::field() ?>
    <div class="row g-3">
        <div class="col-md-6">
            <label>First name</label>
            <input type="text" name="first_name" class="form-control" required value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
        </div>
        <div class="col-md-6">
            <label>Last name</label>
            <input type="text" name="last_name" class="form-control" required value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
        </div>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <label>Middle name <small class="text-muted">(optional)</small></label>
            <input type="text" name="middle_name" class="form-control" value="<?= htmlspecialchars($_POST['middle_name'] ?? '') ?>">
        </div>
        <div class="col-md-6">
            <label>Mobile number</label>
            <input type="tel" name="phone" class="form-control" placeholder="09XX XXX XXXX" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
        </div>
    </div>
    <div>
        <label>Email address</label>
        <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required minlength="8">
            <small class="text-muted">Min 8 chars, letters and numbers.</small>
        </div>
        <div class="col-md-6">
            <label>Confirm password</label>
            <input type="password" name="confirm_password" class="form-control" required minlength="8">
        </div>
    </div>
    <button type="submit" class="btn btn-gold w-100 py-3">Create account</button>
</form>

<div class="auth-links">
    <span>Already have an account?</span>
    <a href="login.php">Sign in</a>
</div>

<?php require_once __DIR__ . '/../includes/auth_footer.php'; ?>
