<?php
/**
 * Admin Logout
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/bootstrap.php';

if (Session::isAdmin()) {
    ActivityLog::record(
        type: LOG_LOGOUT,
        description: 'Admin logged out: ' . Session::userEmail(),
        adminId: Session::userId(),
        severity: LOG_SEVERITY_INFO
    );
}

Session::destroy();
Session::flash('info', 'You have been signed out of the admin portal.');
Redirect::to('admin_login');
