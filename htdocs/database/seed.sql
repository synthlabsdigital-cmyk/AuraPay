-- =====================================================================
-- AuraPay — Seed Data
-- Run AFTER schema.sql: mysql -u root -p aurapay < database/seed.sql
-- =====================================================================
SET NAMES utf8mb4;
USE `aurapay`;

-- ---------------------------------------------------------------------
-- Default admin user
-- Password: Admin@12345  (bcrypt hash below)
-- ---------------------------------------------------------------------
INSERT INTO `users`
    (`first_name`,`last_name`,`email`,`password_hash`,`phone`,`user_type`,`role`,`status`,`email_verified_at`)
VALUES
    ('System','Administrator','admin@aurapay.ph',
     '$2y$10$E9p1k2q3J4v5w6x7y8z9A0b1c2d3e4f5g6h7i8j9k0l1m2n3o4p5q',
     '+639000000000','admin','super_admin','active', NOW());

-- NOTE: The hash above is a placeholder. The installer (helpers/install.php)
-- will generate the real bcrypt hash for the default admin on first run.
-- To generate manually in PHP:
--   php -r "echo password_hash('Admin@12345', PASSWORD_BCRYPT);"

-- ---------------------------------------------------------------------
-- Default application settings
-- ---------------------------------------------------------------------
INSERT INTO `settings` (`key_name`,`value`,`group_name`,`description`) VALUES
    ('app_status',           'active',      'system',  'Application status: active or maintenance'),
    ('app_name',             'AuraPay',     'branding','Application display name'),
    ('support_email',        'support@aurapay.ph', 'contact','Support email address'),
    ('support_phone',        '+63 2 8888 8888',    'contact','Support phone number'),
    ('min_loan_amount',      '5000',        'loan',    'Minimum loan amount in PHP'),
    ('max_loan_amount',      '50000',       'loan',    'Maximum loan amount in PHP'),
    ('min_loan_term',        '3',           'loan',    'Minimum loan term in months'),
    ('max_loan_term',        '12',          'loan',    'Maximum loan term in months'),
    ('default_interest_rate','3.5',         'loan',    'Default monthly interest rate (%)'),
    ('default_processing_fee','150',        'loan',    'Default processing fee in PHP'),
    ('late_penalty_rate',    '2.0',         'loan',    'Late payment penalty rate (%)'),
    ('grace_days',           '3',           'loan',    'Grace period days before penalty'),
    ('credit_min_score',     '300',         'credit',  'Minimum credit score'),
    ('credit_max_score',     '850',         'credit',  'Maximum credit score'),
    ('credit_approval_threshold','580',     'credit',  'Minimum score for loan approval'),
    ('otp_expiry_minutes',   '10',          'security','OTP expiry in minutes'),
    ('otp_max_attempts',     '5',           'security','Maximum OTP attempts'),
    ('session_lifetime',     '7200',        'security','Session lifetime in seconds'),
    ('max_upload_size',      '5242880',     'upload',  'Max upload size in bytes (5MB)'),
    ('allowed_doc_types',    'jpg,jpeg,png,pdf', 'upload','Allowed document file types'),
    ('dev_otp_enabled',      '1',           'security','Show OTP in modal for development (no SMS)'),
    ('default_admin_email',  'admin@aurapay.ph', 'system','Default admin login email');

-- ---------------------------------------------------------------------
-- Default maintenance message (inactive)
-- ---------------------------------------------------------------------
INSERT INTO `maintenance_messages`
    (`title`,`message`,`is_active`,`created_by`)
VALUES
    ('Scheduled maintenance',
     'AuraPay is undergoing scheduled maintenance. We will be back shortly.',
     0, 1);

-- End of seed data
