-- ============================================================================
-- Premium Fintech Platform - Database Schema
-- ============================================================================
-- Database: premium_fintech
-- Engine: InnoDB with utf8mb4 character set
-- 
-- This schema implements a simulated fintech funding platform with:
-- - User authentication and role-based access
-- - KYC verification workflows
-- - Funding arrangement management
-- - Double-entry financial ledger
-- - Simulated deposits, withdrawals, and distributions
-- - Audit trails
-- 
-- IMPORT INSTRUCTIONS:
-- 1. Create the database first: CREATE DATABASE premium_fintech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- 2. Import this file using phpMyAdmin or: mysql -u root premium_fintech < schema.sql
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- Drop tables in reverse dependency order (safe for re-imports)
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS distribution_payments;
DROP TABLE IF EXISTS distribution_calculations;
DROP TABLE IF EXISTS financial_result_records;
DROP TABLE IF EXISTS capital_allocations;
DROP TABLE IF EXISTS ledger_entries;
DROP TABLE IF EXISTS ledger_journals;
DROP TABLE IF EXISTS financial_accounts;
DROP TABLE IF EXISTS withdrawal_requests;
DROP TABLE IF EXISTS payment_transactions;
DROP TABLE IF EXISTS agreement_acceptances;
DROP TABLE IF EXISTS funding_agreements;
DROP TABLE IF EXISTS funding_products;
DROP TABLE IF EXISTS kyc_documents;
DROP TABLE IF EXISTS kyc_cases;
DROP TABLE IF EXISTS password_reset_tokens;
DROP TABLE IF EXISTS email_verification_tokens;
DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS user_roles;
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS user_profiles;
DROP TABLE IF EXISTS users;

-- ============================================================================
-- USERS
-- ============================================================================
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    status ENUM('pending', 'active', 'suspended', 'closed') NOT NULL DEFAULT 'pending',
    email_verified_at DATETIME DEFAULT NULL,
    last_login_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY idx_users_email (email),
    KEY idx_users_status (status),
    KEY idx_users_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- USER PROFILES (extended profile data)
-- ============================================================================
CREATE TABLE user_profiles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    date_of_birth DATE DEFAULT NULL,
    nationality VARCHAR(100) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    country VARCHAR(100) DEFAULT NULL,
    postal_code VARCHAR(30) DEFAULT NULL,
    occupation VARCHAR(100) DEFAULT NULL,
    source_of_funds VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY idx_profiles_user_id (user_id),
    CONSTRAINT fk_profiles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- ROLES
-- ============================================================================
CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY idx_roles_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- PERMISSIONS
-- ============================================================================
CREATE TABLE permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    display_name VARCHAR(200) NOT NULL,
    description TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY idx_permissions_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- ROLE-PERMISSION ASSIGNMENTS
-- ============================================================================
CREATE TABLE role_permissions (
    role_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    CONSTRAINT fk_rp_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    CONSTRAINT fk_rp_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- USER-ROLE ASSIGNMENTS
-- ============================================================================
CREATE TABLE user_roles (
    user_id INT UNSIGNED NOT NULL,
    role_id INT UNSIGNED NOT NULL,
    assigned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, role_id),
    CONSTRAINT fk_ur_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_ur_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- KYC CASES (simulated identity verification)
-- ============================================================================
CREATE TABLE kyc_cases (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    status ENUM('not_started', 'pending', 'approved', 'rejected', 'requires_review') NOT NULL DEFAULT 'not_started',
    verification_reference VARCHAR(100) DEFAULT NULL,
    provider_name VARCHAR(100) DEFAULT 'SimulatedKYC',
    simulation_result VARCHAR(50) DEFAULT NULL COMMENT 'Simulated result: pass, fail, review',
    reviewer_id INT UNSIGNED DEFAULT NULL COMMENT 'Admin who reviewed (if manual)',
    reviewer_notes TEXT DEFAULT NULL,
    submitted_at DATETIME DEFAULT NULL,
    reviewed_at DATETIME DEFAULT NULL,
    expires_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_kyc_user (user_id),
    KEY idx_kyc_status (status),
    CONSTRAINT fk_kyc_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_kyc_reviewer FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- KYC DOCUMENTS (metadata only - no real documents in simulation)
-- ============================================================================
CREATE TABLE kyc_documents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kyc_case_id INT UNSIGNED NOT NULL,
    document_type VARCHAR(50) NOT NULL COMMENT 'national_id, passport, utility_bill, etc.',
    filename VARCHAR(255) DEFAULT NULL,
    file_size INT DEFAULT NULL,
    status ENUM('pending', 'verified', 'rejected') NOT NULL DEFAULT 'pending',
    uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    verified_at DATETIME DEFAULT NULL,
    KEY idx_kyc_docs_case (kyc_case_id),
    CONSTRAINT fk_kyc_docs_case FOREIGN KEY (kyc_case_id) REFERENCES kyc_cases(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- FUNDING PRODUCTS (configurable funding arrangement types)
-- ============================================================================
CREATE TABLE funding_products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(30) NOT NULL COMMENT 'e.g. PROFIT_SHARE_A, PERIODIC_DIST_B',
    name VARCHAR(200) NOT NULL,
    description TEXT DEFAULT NULL,
    product_type ENUM('profit_sharing', 'periodic_distribution') NOT NULL,
    -- Profit sharing percentage (stored as DECIMAL for precision)
    profit_share_pct DECIMAL(5,2) NOT NULL COMMENT 'Funder share percentage, e.g. 60.00',
    -- For periodic distributions
    distribution_period ENUM('monthly', 'quarterly', 'semi_annual', 'annual') DEFAULT NULL,
    -- Fees
    management_fee_pct DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Annual management fee percentage',
    entry_fee_pct DECIMAL(5,2) DEFAULT 0.00 COMMENT 'One-time entry fee percentage',
    -- Withdrawal conditions
    min_lock_in_days INT DEFAULT 0 COMMENT 'Minimum days before withdrawal allowed',
    withdrawal_notice_days INT DEFAULT 30 COMMENT 'Days notice required for withdrawal',
    -- Loss treatment
    loss_carry_forward TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Whether losses carry forward',
    -- Status
    status ENUM('active', 'inactive', 'archived') NOT NULL DEFAULT 'active',
    -- Version tracking
    version INT UNSIGNED NOT NULL DEFAULT 1,
    version_notes TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY idx_products_code (code),
    KEY idx_products_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- FUNDING AGREEMENTS (the contractual terms for each product version)
-- ============================================================================
CREATE TABLE funding_agreements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    version INT UNSIGNED NOT NULL DEFAULT 1,
    title VARCHAR(300) NOT NULL,
    body TEXT NOT NULL COMMENT 'Full agreement text',
    -- Key terms summary
    profit_share_pct DECIMAL(5,2) NOT NULL,
    distribution_period VARCHAR(30) DEFAULT NULL,
    management_fee_pct DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    entry_fee_pct DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    min_lock_in_days INT NOT NULL DEFAULT 0,
    loss_carry_forward TINYINT(1) NOT NULL DEFAULT 1,
    -- Metadata
    effective_from DATE NOT NULL,
    effective_until DATE DEFAULT NULL,
    is_current TINYINT(1) NOT NULL DEFAULT 1,
    created_by INT UNSIGNED DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_agreements_product (product_id),
    KEY idx_agreements_current (is_current),
    CONSTRAINT fk_agreements_product FOREIGN KEY (product_id) REFERENCES funding_products(id) ON DELETE CASCADE,
    CONSTRAINT fk_agreements_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY uk_product_version (product_id, version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- AGREEMENT ACCEPTANCES (record of user accepting a specific agreement version)
-- ============================================================================
CREATE TABLE agreement_acceptances (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    agreement_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    accepted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    -- Snapshot of key terms at acceptance time
    profit_share_pct DECIMAL(5,2) NOT NULL,
    management_fee_pct DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    entry_fee_pct DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    superseded_at DATETIME DEFAULT NULL,
    KEY idx_acceptances_user (user_id),
    KEY idx_acceptances_agreement (agreement_id),
    KEY idx_acceptances_active (is_active),
    CONSTRAINT fk_accept_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_accept_agreement FOREIGN KEY (agreement_id) REFERENCES funding_agreements(id) ON DELETE RESTRICT,
    CONSTRAINT fk_accept_product FOREIGN KEY (product_id) REFERENCES funding_products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- FINANCIAL ACCOUNTS (each user has financial accounts for tracking balances)
-- ============================================================================
CREATE TABLE financial_accounts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED DEFAULT NULL COMMENT 'NULL for system accounts',
    account_code VARCHAR(30) NOT NULL UNIQUE,
    account_name VARCHAR(200) NOT NULL,
    account_type ENUM('asset', 'liability', 'equity', 'revenue', 'expense') NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'UGX',
    balance DECIMAL(20,0) NOT NULL DEFAULT 0 COMMENT 'Cached balance - must match sum of ledger entries',
    status ENUM('active', 'frozen', 'closed') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_accounts_user (user_id),
    KEY idx_accounts_type (account_type),
    CONSTRAINT fk_accounts_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- LEDGER JOURNALS (each transaction is a journal with balanced entries)
-- ============================================================================
CREATE TABLE ledger_journals (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(64) NOT NULL COMMENT 'Unique transaction reference',
    description VARCHAR(500) NOT NULL,
    journal_type ENUM(
        'test_deposit',
        'capital_allocation',
        'capital_deallocation',
        'realized_profit',
        'realized_loss',
        'distribution_accrual',
        'distribution_payment',
        'withdrawal_request',
        'withdrawal_payment',
        'refund',
        'fee',
        'reversal',
        'adjustment'
    ) NOT NULL,
    total_amount DECIMAL(20,0) NOT NULL DEFAULT 0,
    currency VARCHAR(3) NOT NULL DEFAULT 'UGX',
    status ENUM('posted', 'voided') NOT NULL DEFAULT 'posted',
    related_entity_type VARCHAR(50) DEFAULT NULL COMMENT 'e.g. payment_transaction, withdrawal_request',
    related_entity_id INT UNSIGNED DEFAULT NULL,
    created_by INT UNSIGNED DEFAULT NULL,
    approved_by INT UNSIGNED DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY idx_journals_ref (reference),
    KEY idx_journals_type (journal_type),
    KEY idx_journals_related (related_entity_type, related_entity_id),
    KEY idx_journals_status (status),
    CONSTRAINT fk_journals_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_journals_approver FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- LEDGER ENTRIES (double-entry: each journal has at least 2 entries that balance)
-- ============================================================================
CREATE TABLE ledger_entries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    journal_id INT UNSIGNED NOT NULL,
    account_id INT UNSIGNED NOT NULL,
    entry_type ENUM('debit', 'credit') NOT NULL,
    amount DECIMAL(20,0) NOT NULL CHECK (amount >= 0),
    currency VARCHAR(3) NOT NULL DEFAULT 'UGX',
    description VARCHAR(500) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_entries_journal (journal_id),
    KEY idx_entries_account (account_id),
    KEY idx_entries_type (entry_type),
    CONSTRAINT fk_entries_journal FOREIGN KEY (journal_id) REFERENCES ledger_journals(id) ON DELETE CASCADE,
    CONSTRAINT fk_entries_account FOREIGN KEY (account_id) REFERENCES financial_accounts(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- PAYMENT TRANSACTIONS (simulated deposits)
-- ============================================================================
CREATE TABLE payment_transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    reference VARCHAR(64) NOT NULL UNIQUE COMMENT 'Unique payment reference',
    amount DECIMAL(20,0) NOT NULL CHECK (amount > 0),
    currency VARCHAR(3) NOT NULL DEFAULT 'UGX',
    status ENUM('pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded') NOT NULL DEFAULT 'pending',
    payment_method VARCHAR(50) NOT NULL DEFAULT 'test_gateway',
    gateway_reference VARCHAR(100) DEFAULT NULL,
    -- Idempotency
    idempotency_key VARCHAR(64) DEFAULT NULL,
    -- Status tracking
    status_history TEXT DEFAULT NULL COMMENT 'JSON array of status changes',
    notes TEXT DEFAULT NULL,
    completed_at DATETIME DEFAULT NULL,
    failed_at DATETIME DEFAULT NULL,
    cancelled_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_payments_user (user_id),
    KEY idx_payments_status (status),
    KEY idx_payments_reference (reference),
    KEY idx_payments_idempotency (idempotency_key),
    CONSTRAINT fk_payments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- CAPITAL ALLOCATIONS (records of capital allocated to the funding arrangement)
-- ============================================================================
CREATE TABLE capital_allocations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    payment_id INT UNSIGNED DEFAULT NULL,
    amount DECIMAL(20,0) NOT NULL CHECK (amount > 0),
    currency VARCHAR(3) NOT NULL DEFAULT 'UGX',
    status ENUM('allocated', 'deallocated', 'partially_released') NOT NULL DEFAULT 'allocated',
    allocated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deallocated_at DATETIME DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_by INT UNSIGNED DEFAULT NULL,
    KEY idx_allocations_user (user_id),
    KEY idx_allocations_status (status),
    CONSTRAINT fk_allocations_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_allocations_payment FOREIGN KEY (payment_id) REFERENCES payment_transactions(id) ON DELETE SET NULL,
    CONSTRAINT fk_allocations_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- FINANCIAL RESULT RECORDS (simulated trading results)
-- ============================================================================
CREATE TABLE financial_result_records (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    total_capital DECIMAL(20,0) NOT NULL COMMENT 'Total capital under management during period',
    gross_result DECIMAL(20,0) NOT NULL COMMENT 'Gross profit/loss (negative = loss)',
    fees_deducted DECIMAL(20,0) NOT NULL DEFAULT 0,
    net_result DECIMAL(20,0) NOT NULL COMMENT 'Net profit/loss after fees',
    currency VARCHAR(3) NOT NULL DEFAULT 'UGX',
    status ENUM('draft', 'pending_review', 'approved', 'rejected') NOT NULL DEFAULT 'draft',
    created_by INT UNSIGNED DEFAULT NULL,
    reviewed_by INT UNSIGNED DEFAULT NULL,
    reviewed_at DATETIME DEFAULT NULL,
    review_notes TEXT DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_results_period (period_start, period_end),
    KEY idx_results_status (status),
    CONSTRAINT fk_results_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_results_reviewer FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- DISTRIBUTION CALCULATIONS (profit distribution to funders)
-- ============================================================================
CREATE TABLE distribution_calculations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    result_record_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    allocation_id INT UNSIGNED NOT NULL,
    -- Calculation details
    capital_amount DECIMAL(20,0) NOT NULL,
    profit_share_pct DECIMAL(5,2) NOT NULL,
    gross_profit_share DECIMAL(20,0) NOT NULL,
    fees_deducted DECIMAL(20,0) NOT NULL DEFAULT 0,
    loss_carryforward DECIMAL(20,0) NOT NULL DEFAULT 0,
    net_distribution DECIMAL(20,0) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'UGX',
    status ENUM('calculated', 'approved', 'paid', 'cancelled') NOT NULL DEFAULT 'calculated',
    calculated_by INT UNSIGNED DEFAULT NULL,
    approved_by INT UNSIGNED DEFAULT NULL,
    approved_at DATETIME DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_dist_calc_result (result_record_id),
    KEY idx_dist_calc_user (user_id),
    KEY idx_dist_calc_status (status),
    CONSTRAINT fk_dist_calc_result FOREIGN KEY (result_record_id) REFERENCES financial_result_records(id) ON DELETE CASCADE,
    CONSTRAINT fk_dist_calc_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_dist_calc_allocation FOREIGN KEY (allocation_id) REFERENCES capital_allocations(id) ON DELETE RESTRICT,
    CONSTRAINT fk_dist_calc_calculator FOREIGN KEY (calculated_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_dist_calc_approver FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- DISTRIBUTION PAYMENTS (actual distributions paid to funders)
-- ============================================================================
CREATE TABLE distribution_payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    distribution_calc_id INT UNSIGNED NOT NULL,
    amount DECIMAL(20,0) NOT NULL CHECK (amount > 0),
    currency VARCHAR(3) NOT NULL DEFAULT 'UGX',
    status ENUM('pending', 'completed', 'failed') NOT NULL DEFAULT 'pending',
    payment_reference VARCHAR(64) DEFAULT NULL,
    paid_at DATETIME DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_dist_pay_calc (distribution_calc_id),
    CONSTRAINT fk_dist_pay_calc FOREIGN KEY (distribution_calc_id) REFERENCES distribution_calculations(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- WITHDRAWAL REQUESTS
-- ============================================================================
CREATE TABLE withdrawal_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    reference VARCHAR(64) NOT NULL UNIQUE,
    amount DECIMAL(20,0) NOT NULL CHECK (amount > 0),
    currency VARCHAR(3) NOT NULL DEFAULT 'UGX',
    status ENUM('pending', 'approved', 'processing', 'completed', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
    -- Approval workflow
    approved_by INT UNSIGNED DEFAULT NULL,
    approved_at DATETIME DEFAULT NULL,
    rejection_reason TEXT DEFAULT NULL,
    -- Payment details
    payment_method VARCHAR(100) DEFAULT NULL,
    payment_reference VARCHAR(100) DEFAULT NULL,
    processed_at DATETIME DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_withdrawals_user (user_id),
    KEY idx_withdrawals_status (status),
    KEY idx_withdrawals_reference (reference),
    CONSTRAINT fk_withdrawals_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_withdrawals_approver FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- NOTIFICATIONS
-- ============================================================================
CREATE TABLE notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    type VARCHAR(50) NOT NULL COMMENT 'e.g. deposit_confirmed, withdrawal_approved, kyc_approved',
    title VARCHAR(300) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    related_type VARCHAR(50) DEFAULT NULL,
    related_id INT UNSIGNED DEFAULT NULL,
    read_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_notif_user (user_id),
    KEY idx_notif_read (is_read),
    KEY idx_notif_type (type),
    CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- AUDIT LOGS
-- ============================================================================
CREATE TABLE audit_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100) DEFAULT NULL,
    entity_id INT UNSIGNED DEFAULT NULL,
    old_values JSON DEFAULT NULL,
    new_values JSON DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    description TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_audit_user (user_id),
    KEY idx_audit_action (action),
    KEY idx_audit_entity (entity_type, entity_id),
    KEY idx_audit_created (created_at),
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- PASSWORD RESET TOKENS
-- ============================================================================
CREATE TABLE password_reset_tokens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_reset_user (user_id),
    KEY idx_reset_token (token),
    CONSTRAINT fk_reset_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- EMAIL VERIFICATION TOKENS
-- ============================================================================
CREATE TABLE email_verification_tokens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    verified_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_verify_user (user_id),
    KEY idx_verify_token (token),
    CONSTRAINT fk_verify_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- LOGIN ATTEMPTS (for rate limiting tracking in database)
-- ============================================================================
CREATE TABLE login_attempts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    success TINYINT(1) NOT NULL DEFAULT 0,
    attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_login_email (email),
    KEY idx_login_ip (ip_address),
    KEY idx_login_attempted (attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
