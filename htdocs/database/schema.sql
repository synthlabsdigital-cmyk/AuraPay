-- =====================================================================
-- AuraPay — Lending Platform Framework
-- Database Schema (MySQL 8.0+ / MariaDB 10.6+)
-- =====================================================================
-- This file is the single source of truth for the database structure.
-- Run via: mysql -u root -p < database/schema.sql
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;
SET sql_mode = 'STRICT_ALL_TABLES';

-- ---------------------------------------------------------------------
-- Database
-- ---------------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `aurapay`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

USE `aurapay`;

-- ---------------------------------------------------------------------
-- users
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id`                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `first_name`          VARCHAR(100)    NOT NULL,
    `middle_name`         VARCHAR(100)    DEFAULT NULL,
    `last_name`           VARCHAR(100)    NOT NULL,
    `email`               VARCHAR(190)    NOT NULL,
    `password_hash`       VARCHAR(255)    NOT NULL,
    `phone`               VARCHAR(32)     NOT NULL,
    `user_type`           ENUM('customer','admin') NOT NULL DEFAULT 'customer',
    `role`                VARCHAR(50)     DEFAULT NULL,
    `status`              ENUM('active','inactive','suspended','pending') NOT NULL DEFAULT 'pending',
    `email_verified_at`   DATETIME        DEFAULT NULL,
    `last_login_at`       DATETIME        DEFAULT NULL,
    `failed_login_attempts` INT UNSIGNED  NOT NULL DEFAULT 0,
    `locked_until`        DATETIME        DEFAULT NULL,
    `created_at`          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_users_email` (`email`),
    KEY `idx_users_type` (`user_type`),
    KEY `idx_users_status` (`status`),
    KEY `idx_users_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- user_profiles
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `user_profiles`;
CREATE TABLE `user_profiles` (
    `id`                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`             BIGINT UNSIGNED NOT NULL,
    `date_of_birth`       DATE            DEFAULT NULL,
    `gender`              ENUM('male','female','other') DEFAULT NULL,
    `nationality`         VARCHAR(80)     DEFAULT 'Filipino',
    `civil_status`        ENUM('single','married','divorced','widowed') DEFAULT NULL,
    `present_address`     TEXT            DEFAULT NULL,
    `permanent_address`   TEXT            DEFAULT NULL,
    `region`              VARCHAR(100)    DEFAULT NULL,
    `province`            VARCHAR(100)    DEFAULT NULL,
    `city`                VARCHAR(100)    DEFAULT NULL,
    `barangay`            VARCHAR(100)    DEFAULT NULL,
    `postal_code`         VARCHAR(20)     DEFAULT NULL,
    `employment_status`   ENUM('employed','self_employed','unemployed','retired','student') DEFAULT NULL,
    `employer`            VARCHAR(190)    DEFAULT NULL,
    `job_title`           VARCHAR(150)    DEFAULT NULL,
    `monthly_income`      DECIMAL(14,2)   DEFAULT NULL,
    `years_employed`      DECIMAL(4,1)    DEFAULT NULL,
    `business_name`       VARCHAR(190)    DEFAULT NULL,
    `business_type`       VARCHAR(150)    DEFAULT NULL,
    `source_of_funds`     VARCHAR(150)    DEFAULT NULL,
    `bank_name`           VARCHAR(190)    DEFAULT NULL,
    `bank_account_number` VARCHAR(40)     DEFAULT NULL,
    `bank_account_name`   VARCHAR(190)    DEFAULT NULL,
    `ewallet_provider`    VARCHAR(50)     DEFAULT NULL,
    `ewallet_number`      VARCHAR(40)     DEFAULT NULL,
    `id_type`             VARCHAR(50)     DEFAULT NULL,
    `id_number`           VARCHAR(80)     DEFAULT NULL,
    `id_issue_date`       DATE            DEFAULT NULL,
    `id_expiry_date`      DATE            DEFAULT NULL,
    `mothers_maiden_name` VARCHAR(190)    DEFAULT NULL,
    `emergency_contact_name` VARCHAR(190) DEFAULT NULL,
    `emergency_contact_phone` VARCHAR(32) DEFAULT NULL,
    `emergency_contact_relation` VARCHAR(80) DEFAULT NULL,
    `profile_completed`   TINYINT(1)      NOT NULL DEFAULT 0,
    `completed_at`        DATETIME        DEFAULT NULL,
    `created_at`          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_profile_user` (`user_id`),
    KEY `idx_profile_region` (`region`),
    KEY `idx_profile_employment` (`employment_status`),
    CONSTRAINT `fk_profile_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- otp_codes
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `otp_codes`;
CREATE TABLE `otp_codes` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`     BIGINT UNSIGNED NOT NULL,
    `code`        VARCHAR(10)     NOT NULL,
    `purpose`     ENUM('registration','login','password_reset') NOT NULL DEFAULT 'registration',
    `attempts`    INT UNSIGNED    NOT NULL DEFAULT 0,
    `expires_at`  DATETIME        NOT NULL,
    `verified_at` DATETIME        DEFAULT NULL,
    `consumed`    TINYINT(1)      NOT NULL DEFAULT 0,
    `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_otp_user` (`user_id`),
    KEY `idx_otp_code` (`code`),
    KEY `idx_otp_purpose` (`purpose`),
    CONSTRAINT `fk_otp_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- documents
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`       BIGINT UNSIGNED NOT NULL,
    `document_type` ENUM('government_id','proof_of_income','proof_of_billing','selfie','supporting_document') NOT NULL,
    `file_name`     VARCHAR(255)    NOT NULL,
    `file_path`     VARCHAR(500)    NOT NULL,
    `file_size`     INT UNSIGNED    NOT NULL DEFAULT 0,
    `mime_type`     VARCHAR(100)    NOT NULL,
    `status`        ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending',
    `rejection_reason` TEXT        DEFAULT NULL,
    `verified_by`   BIGINT UNSIGNED DEFAULT NULL,
    `verified_at`   DATETIME        DEFAULT NULL,
    `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_doc_user` (`user_id`),
    KEY `idx_doc_type` (`document_type`),
    KEY `idx_doc_status` (`status`),
    CONSTRAINT `fk_doc_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- credit_evaluations
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `credit_evaluations`;
CREATE TABLE `credit_evaluations` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`         BIGINT UNSIGNED NOT NULL,
    `score`           INT UNSIGNED    NOT NULL,
    `rating`          VARCHAR(20)     NOT NULL,
    `employment_score` INT UNSIGNED   NOT NULL DEFAULT 0,
    `income_score`    INT UNSIGNED    NOT NULL DEFAULT 0,
    `documents_score` INT UNSIGNED    NOT NULL DEFAULT 0,
    `identity_score`  INT UNSIGNED    NOT NULL DEFAULT 0,
    `history_score`   INT UNSIGNED    NOT NULL DEFAULT 0,
    `recommended_amount` DECIMAL(14,2) NOT NULL DEFAULT 0,
    `recommended_term`   INT UNSIGNED NOT NULL DEFAULT 0,
    `risk_level`      ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
    `remarks`         TEXT            DEFAULT NULL,
    `status`          ENUM('pending','completed','failed') NOT NULL DEFAULT 'pending',
    `evaluated_at`    DATETIME        DEFAULT NULL,
    `created_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_credit_user` (`user_id`),
    KEY `idx_credit_status` (`status`),
    CONSTRAINT `fk_credit_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- loans
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `loans`;
CREATE TABLE `loans` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `loan_reference`    VARCHAR(30)     NOT NULL,
    `user_id`           BIGINT UNSIGNED NOT NULL,
    `credit_evaluation_id` BIGINT UNSIGNED DEFAULT NULL,
    `principal_amount`  DECIMAL(14,2)   NOT NULL,
    `interest_rate`     DECIMAL(6,3)    NOT NULL,
    `term_months`       INT UNSIGNED    NOT NULL,
    `processing_fee`    DECIMAL(14,2)   NOT NULL DEFAULT 0,
    `total_interest`    DECIMAL(14,2)   NOT NULL DEFAULT 0,
    `total_payable`    DECIMAL(14,2)   NOT NULL DEFAULT 0,
    `monthly_payment`  DECIMAL(14,2)   NOT NULL DEFAULT 0,
    `outstanding_balance` DECIMAL(14,2) NOT NULL DEFAULT 0,
    `amount_paid`       DECIMAL(14,2)   NOT NULL DEFAULT 0,
    `status`            ENUM('pending','under_review','approved','rejected','disbursed','active','completed','defaulted','closed') NOT NULL DEFAULT 'pending',
    `purpose`           VARCHAR(255)    DEFAULT NULL,
    `application_date`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `approval_date`     DATETIME        DEFAULT NULL,
    `rejection_date`    DATETIME        DEFAULT NULL,
    `rejection_reason`  TEXT            DEFAULT NULL,
    `disbursement_date` DATETIME        DEFAULT NULL,
    `disbursement_method` ENUM('bank_transfer','gcash','maya','cash','over_the_counter') DEFAULT NULL,
    `disbursement_reference` VARCHAR(100) DEFAULT NULL,
    `first_payment_date` DATE           DEFAULT NULL,
    `maturity_date`     DATE            DEFAULT NULL,
    `completed_date`    DATE            DEFAULT NULL,
    `reviewed_by`       BIGINT UNSIGNED  DEFAULT NULL,
    `approved_by`       BIGINT UNSIGNED  DEFAULT NULL,
    `disbursed_by`      BIGINT UNSIGNED  DEFAULT NULL,
    `admin_notes`       TEXT            DEFAULT NULL,
    `created_at`        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_loan_ref` (`loan_reference`),
    KEY `idx_loan_user` (`user_id`),
    KEY `idx_loan_status` (`status`),
    KEY `idx_loan_date` (`application_date`),
    CONSTRAINT `fk_loan_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_loan_credit` FOREIGN KEY (`credit_evaluation_id`) REFERENCES `credit_evaluations`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- loan_amortization
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `loan_amortization`;
CREATE TABLE `loan_amortization` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `loan_id`         BIGINT UNSIGNED NOT NULL,
    `installment_number` INT UNSIGNED NOT NULL,
    `due_date`        DATE            NOT NULL,
    `principal_component` DECIMAL(14,2) NOT NULL,
    `interest_component`  DECIMAL(14,2) NOT NULL,
    `installment_amount`  DECIMAL(14,2) NOT NULL,
    `balance_after`       DECIMAL(14,2) NOT NULL,
    `status`          ENUM('pending','paid','overdue','partial') NOT NULL DEFAULT 'pending',
    `paid_amount`     DECIMAL(14,2)   NOT NULL DEFAULT 0,
    `paid_date`       DATE            DEFAULT NULL,
    `penalty_amount`  DECIMAL(14,2)   NOT NULL DEFAULT 0,
    `created_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_amort_loan_inst` (`loan_id`,`installment_number`),
    KEY `idx_amort_due` (`due_date`),
    KEY `idx_amort_status` (`status`),
    CONSTRAINT `fk_amort_loan` FOREIGN KEY (`loan_id`) REFERENCES `loans`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- transactions
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `transaction_reference` VARCHAR(30) NOT NULL,
    `loan_id`         BIGINT UNSIGNED DEFAULT NULL,
    `user_id`         BIGINT UNSIGNED NOT NULL,
    `type`            ENUM('disbursement','repayment','fee','interest','penalty','adjustment') NOT NULL,
    `amount`          DECIMAL(14,2)   NOT NULL,
    `payment_method`  ENUM('cash','bank_transfer','gcash','maya','card','over_the_counter') DEFAULT NULL,
    `payment_reference` VARCHAR(100)  DEFAULT NULL,
    `status`          ENUM('pending','completed','failed') NOT NULL DEFAULT 'pending',
    `description`     VARCHAR(255)    DEFAULT NULL,
    `processed_by`    BIGINT UNSIGNED DEFAULT NULL,
    `transaction_date` DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_tx_ref` (`transaction_reference`),
    KEY `idx_tx_loan` (`loan_id`),
    KEY `idx_tx_user` (`user_id`),
    KEY `idx_tx_type` (`type`),
    KEY `idx_tx_status` (`status`),
    KEY `idx_tx_date` (`transaction_date`),
    CONSTRAINT `fk_tx_loan` FOREIGN KEY (`loan_id`) REFERENCES `loans`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_tx_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- notifications
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`     BIGINT UNSIGNED NOT NULL,
    `type`        ENUM('info','success','warning','error','loan','payment','document','system') NOT NULL DEFAULT 'info',
    `title`       VARCHAR(255)    NOT NULL,
    `message`     TEXT            NOT NULL,
    `link`        VARCHAR(255)    DEFAULT NULL,
    `is_read`     TINYINT(1)      NOT NULL DEFAULT 0,
    `read_at`     DATETIME        DEFAULT NULL,
    `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_notif_user` (`user_id`),
    KEY `idx_notif_read` (`is_read`),
    CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- activity_logs
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `admin_id`      BIGINT UNSIGNED DEFAULT NULL,
    `user_id`       BIGINT UNSIGNED DEFAULT NULL,
    `type`          VARCHAR(50)     NOT NULL,
    `severity`       ENUM('info','warning','critical') NOT NULL DEFAULT 'info',
    `description`   TEXT            NOT NULL,
    `entity_type`   VARCHAR(50)     DEFAULT NULL,
    `entity_id`     BIGINT UNSIGNED DEFAULT NULL,
    `ip_address`    VARCHAR(45)     DEFAULT NULL,
    `user_agent`     VARCHAR(255)    DEFAULT NULL,
    `metadata`      JSON            DEFAULT NULL,
    `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_log_admin` (`admin_id`),
    KEY `idx_log_user` (`user_id`),
    KEY `idx_log_type` (`type`),
    KEY `idx_log_severity` (`severity`),
    KEY `idx_log_entity` (`entity_type`,`entity_id`),
    KEY `idx_log_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- settings (key-value application configuration)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key_name`    VARCHAR(100)    NOT NULL,
    `value`       TEXT           DEFAULT NULL,
    `group_name`  VARCHAR(50)    DEFAULT 'general',
    `description` VARCHAR(255)   DEFAULT NULL,
    `updated_by`  BIGINT UNSIGNED DEFAULT NULL,
    `created_at`  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_setting_key` (`key_name`),
    KEY `idx_setting_group` (`group_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- maintenance_messages
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `maintenance_messages`;
CREATE TABLE `maintenance_messages` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`      VARCHAR(255)    NOT NULL,
    `message`    TEXT            NOT NULL,
    `start_at`   DATETIME        DEFAULT NULL,
    `end_at`     DATETIME        DEFAULT NULL,
    `is_active`  TINYINT(1)      NOT NULL DEFAULT 1,
    `created_by` BIGINT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_maint_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
