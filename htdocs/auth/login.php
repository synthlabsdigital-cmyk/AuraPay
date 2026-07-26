<?php
/**
 * Login Page
 *
 * Customer email/password login.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Welcome back';
$authSubtitle = 'Sign in to your private account.';
require_once __DIR__ . '/../includes/auth_header.php';

if (Session::isCustomer()) {
    Redirect::to('dashboard');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $result = Auth::attempt($email, $password, USER_TYPE_CUSTOMER);
    if ($result['success']) {
        Session::flash('success', 'Welcome back, ' . $result['user']['first_name'] . '!');
        Redirect::to('dashboard');
    } else {
        Session::flash('error', $result['message']);
    }
}
?>

<form method="post" class="auth-form" novalidate>
    <?= Csrf::field() ?>
    <div>
        <label>Email address</label>
        <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" autofocus>
    </div>
    <div>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="mb-0">Password</label>
            <a href="forgot_password.php" class="small" style="color: var(--gold-bright);">Forgot?</a>
        </div>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-gold w-100 py-3">Sign in</button>
</form>

<div class="auth-links">
    <span>Don't have an account?</span>
    <a href="register.php">Create one</a>
</div>

<?php require_once __DIR__ . '/../includes/auth_footer.php'; ?>
