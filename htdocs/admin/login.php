<?php
/**
 * Admin Login Page
 *
 * Hidden admin login. Uses the same auth engine but expects user_type = admin.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
$pageTitle = 'Control Center';
$authSubtitle = 'Restricted access. Authorized personnel only.';
$authVariant = 'admin';
require_once __DIR__ . '/../includes/auth_header.php';

if (Session::isAdmin()) {
    Redirect::to('admin_dashboard');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::check();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $result = Auth::attempt($email, $password, USER_TYPE_ADMIN);
    if ($result['success']) {
        Session::flash('success', 'Welcome to the admin portal.');
        Redirect::to('admin_dashboard');
    }
    Session::flash('error', $result['message']);
}
?>

<div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3 mb-4" style="background: var(--gold-soft); border: 1px solid rgba(212,175,122,0.2);">
    <i class="bi bi-shield-check" style="color: var(--gold-bright); font-size: 0.9rem;"></i>
    <span style="color: var(--gold-bright); font-size: 0.75rem;">Administrative access is monitored and audited.</span>
</div>

<form method="post" class="auth-form" novalidate>
    <?= Csrf::field() ?>
    <div>
        <label>Email address</label>
        <input type="email" name="email" class="form-control" required autofocus>
    </div>
    <div>
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-gold w-100 py-3">Sign in</button>
</form>

<div class="auth-links">
    <a href="../index.php">&larr; Back to home</a>
</div>

<?php require_once __DIR__ . '/../includes/auth_footer.php'; ?>
