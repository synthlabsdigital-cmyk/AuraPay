<?php
/**
 * Helper Autoloader
 *
 * Loads all helper classes. Each helper is a final class in the helpers/ directory.
 * This file is required once per request via includes/bootstrap.php.
 */

declare(strict_types=1);

$helperFiles = [
    'Database.php',
    'Session.php',
    'Auth.php',
    'Otp.php',
    'Validator.php',
    'File.php',
    'Credit.php',
    'Loan.php',
    'Transaction.php',
    'Notification.php',
    'ActivityLog.php',
    'Settings.php',
    'Redirect.php',
    'Csrf.php',
    'Hash.php',
    'ErrorHelper.php',
    'Util.php',
    'Install.php',
];

foreach ($helperFiles as $file) {
    require_once HELPER_PATH . '/' . $file;
}
