<?php
/**
 * Logout
 *
 * Destroys the session and redirects to login.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/bootstrap.php';

if (Session::isLoggedIn()) {
    ActivityLog::record(
        type: LOG_LOGOUT,
        description: (Session::isAdmin() ? 'Admin' : 'Customer') . ' logged out: ' . Session::userEmail(),
        userId: Session::userId(),
        severity: LOG_SEVERITY_INFO
    );
}

Session::destroy();
Session::flash('info', 'You have been signed out.');
Redirect::to('login');
