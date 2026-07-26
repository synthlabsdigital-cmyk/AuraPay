<?php
/**
 * Constants Configuration
 *
 * Centralised application-wide constants for the Lending Platform Framework.
 */

declare(strict_types=1);

/**
 * Application Status
 */
define('APP_STATUS_ACTIVE',   'active');
define('APP_STATUS_MAINTENANCE', 'maintenance');

/**
 * User Status
 */
define('USER_STATUS_ACTIVE',   'active');
define('USER_STATUS_INACTIVE', 'inactive');
define('USER_STATUS_SUSPENDED', 'suspended');
define('USER_STATUS_PENDING',  'pending');

/**
 * User Types
 */
define('USER_TYPE_CUSTOMER', 'customer');
define('USER_TYPE_ADMIN',    'admin');

/**
 * Admin Roles
 */
define('ROLE_SUPER_ADMIN', 'super_admin');
define('ROLE_ADMIN',       'admin');
define('ROLE_STAFF',       'staff');

/**
 * Document Types
 */
define('DOC_GOV_ID',           'government_id');
define('DOC_PROOF_OF_INCOME',  'proof_of_income');
define('DOC_PROOF_OF_BILLING', 'proof_of_billing');
define('DOC_SELFIE',           'selfie');
define('DOC_SUPPORTING',        'supporting_document');

/**
 * Document Status
 */
define('DOC_STATUS_PENDING',  'pending');
define('DOC_STATUS_VERIFIED', 'verified');
define('DOC_STATUS_REJECTED', 'rejected');

/**
 * Loan Status
 */
define('LOAN_PENDING',          'pending');
define('LOAN_UNDER_REVIEW',     'under_review');
define('LOAN_APPROVED',         'approved');
define('LOAN_REJECTED',         'rejected');
define('LOAN_DISBURSED',        'disbursed');
define('LOAN_ACTIVE',           'active');
define('LOAN_COMPLETED',        'completed');
define('LOAN_DEFAULTED',        'defaulted');
define('LOAN_CLOSED',           'closed');

/**
 * Credit Evaluation Status
 */
define('CREDIT_PENDING',  'pending');
define('CREDIT_COMPLETED','completed');
define('CREDIT_FAILED',   'failed');

/**
 * Transaction Types
 */
define('TX_DISBURSEMENT', 'disbursement');
define('TX_REPAYMENT',    'repayment');
define('TX_FEE',          'fee');
define('TX_INTEREST',     'interest');
define('TX_PENALTY',      'penalty');
define('TX_ADJUSTMENT',   'adjustment');

/**
 * Transaction Status
 */
define('TX_STATUS_PENDING',  'pending');
define('TX_STATUS_COMPLETED','completed');
define('TX_STATUS_FAILED',   'failed');

/**
 * Payment Methods
 */
define('PAY_CASH',       'cash');
define('PAY_BANK',       'bank_transfer');
define('PAY_GCASH',      'gcash');
define('PAY_MAYA',       'maya');
define('PAY_CARD',       'card');
define('PAY_OTC',        'over_the_counter');

/**
 * Notification Types
 */
define('NOTIF_INFO',     'info');
define('NOTIF_SUCCESS',  'success');
define('NOTIF_WARNING',  'warning');
define('NOTIF_ERROR',    'error');
define('NOTIF_LOAN',     'loan');
define('NOTIF_PAYMENT',  'payment');
define('NOTIF_DOCUMENT', 'document');
define('NOTIF_SYSTEM',   'system');

/**
 * Activity Log Types
 */
define('LOG_LOGIN',         'login');
define('LOG_LOGOUT',        'logout');
define('LOG_CREATE',        'create');
define('LOG_UPDATE',        'update');
define('LOG_DELETE',        'delete');
define('LOG_VIEW',          'view');
define('LOG_APPROVE',       'approve');
define('LOG_REJECT',        'reject');
define('LOG_DISBURSE',      'disburse');
define('LOG_PAYMENT',       'payment');
define('LOG_STATUS_CHANGE', 'status_change');
define('LOG_CONFIG',        'configuration');
define('LOG_MAINTENANCE',   'maintenance');
define('LOG_SECURITY',      'security');

/**
 * Activity Log Severity
 */
define('LOG_SEVERITY_INFO',     'info');
define('LOG_SEVERITY_WARNING',  'warning');
define('LOG_SEVERITY_CRITICAL', 'critical');

/**
 * OTP Configuration
 */
define('OTP_LENGTH', 6);
define('OTP_EXPIRY_MINUTES', 10);
define('OTP_MAX_ATTEMPTS', 5);

/**
 * Pagination
 */
define('DEFAULT_PER_PAGE', 10);
define('ADMIN_PER_PAGE',   25);

/**
 * Upload Limits
 */
define('MAX_UPLOAD_SIZE', 5242880);
define('ALLOWED_DOC_TYPES', 'jpg,jpeg,png,pdf');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

/**
 * File System
 */
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH);
define('CONFIG_PATH', ROOT_PATH . '/config');
define('HELPER_PATH', ROOT_PATH . '/helpers');
define('INCLUDE_PATH', ROOT_PATH . '/includes');
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('LOG_PATH', ROOT_PATH . '/logs');
define('STORAGE_PATH', ROOT_PATH . '/storage');
