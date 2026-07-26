<?php
/**
 * Bootstrap
 *
 * Single entry point that every page requires. Loads config, helpers,
 * starts the session, and enforces maintenance mode.
 */

declare(strict_types=1);

// Start output buffering so redirects can discard pending output
if (!ob_get_level()) {
    ob_start();
}

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

// Global exception handler — prevents white screens on uncaught errors
set_exception_handler(function (Throwable $e) use ($appCfg) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');
    }
    if ($appCfg['debug']) {
        echo '<h1>Application Error</h1><pre>' . htmlspecialchars($e->getMessage() . "\n\n" . $e->getTraceAsString()) . '</pre>';
    } else {
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Error</title>';
        echo '<style>body{font-family:system-ui,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;background:#f8fafb;color:#1e293b}';
        echo '.card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:2.5rem;max-width:420px;text-align:center;box-shadow:0 4px 14px rgba(0,0,0,.08)}';
        echo 'h1{font-size:1.25rem;margin:0 0 .5rem}p{color:#64748b;margin:0 0 1.5rem;font-size:.95rem}';
        echo 'a{display:inline-block;padding:.6rem 1.5rem;background:#0F4C81;color:#fff;text-decoration:none;border-radius:8px;font-size:.9rem}</style></head>';
        echo '<body><div class="card"><h1>Something went wrong</h1><p>We encountered an unexpected error. Please try again in a moment.</p>';
        echo '<a href="javascript:history.back()">Try again</a></div></body></html>';
    }
});

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
