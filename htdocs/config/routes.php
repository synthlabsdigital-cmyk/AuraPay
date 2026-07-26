<?php
/**
 * Routes Configuration
 *
 * Central route map for the Lending Platform Framework.
 * Maps logical route names to file paths.
 */

declare(strict_types=1);

return [
    'landing'        => 'index.php',

    // Auth
    'login'          => 'auth/login.php',
    'register'       => 'auth/register.php',
    'verify_otp'     => 'auth/verify_otp.php',
    'logout'         => 'auth/logout.php',
    'forgot'         => 'auth/forgot_password.php',
    'reset'          => 'auth/reset_password.php',

    // Customer
    'dashboard'      => 'pages/dashboard.php',
    'profile'        => 'pages/profile.php',
    'documents'      => 'pages/documents.php',
    'credit'         => 'pages/credit_evaluation.php',
    'apply_loan'     => 'pages/apply_loan.php',
    'loan_history'   => 'pages/loan_history.php',
    'loan_detail'    => 'pages/loan_detail.php',
    'payments'       => 'pages/payments.php',
    'transactions'   => 'pages/transactions.php',
    'timeline'       => 'pages/timeline.php',
    'notifications'  => 'pages/notifications.php',
    'settings'       => 'pages/settings.php',

    // Admin
    'admin_login'    => 'admin/login.php',
    'admin_dashboard'=> 'admin/dashboard.php',
    'admin_customers'=> 'admin/customers.php',
    'admin_apps'     => 'admin/applications.php',
    'admin_loans'    => 'admin/loans.php',
    'admin_credit'   => 'admin/credit_evaluation.php',
    'admin_transactions' => 'admin/transactions.php',
    'admin_reports'  => 'admin/reports.php',
    'admin_logs'     => 'admin/activity_logs.php',
    'admin_config'   => 'admin/configuration.php',
    'admin_maintenance' => 'admin/maintenance.php',
    'admin_logout'   => 'admin/logout.php',
];
