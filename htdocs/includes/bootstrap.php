<?php
/**
 * Bootstrap
 *
 * Single entry point that every page requires. Loads config, helpers,
 * starts the session, and enforces maintenance mode.
 */

declare(strict_types=1);

// Error reporting
$appCfg = require CONFIG_PATH . '/app.php';
if ($appCfg['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', '0');
}

date_default_timezone_set($appCfg['timezone']);

// Load all helpers
require_once HELPER_PATH . '/helpers.php';

// Start session
Session::start();

// Maintenance mode check (skip for admin and auth pages)
$maintenanceBypass = (
    strpos($_SERVER['SCRIPT_NAME'] ?? '', '/admin/') !== false
    || strpos($_SERVER['SCRIPT_NAME'] ?? '', '/auth/') !== false
    || strpos($_SERVER['SCRIPT_NAME'] ?? '', 'index.php') !== false
);

if (!$maintenanceBypass && Settings::isMaintenance()) {
    $msg = Database::fetch('SELECT * FROM maintenance_messages WHERE is_active = 1 ORDER BY id DESC LIMIT 1');
    http_response_code(503);
    include INCLUDE_PATH . '/maintenance.php';
    exit;
}

// Ensure default admin exists on first run
if (Database::tableExists('users') && !Install::isInstalled()) {
    Install::ensureDirectories();
    $adminEmail = Settings::get('default_admin_email', 'admin@aurapay.ph');
    Install::ensureDefaultAdmin($adminEmail, 'Admin@12345');
}
