-- ============================================================================
-- Premium Fintech Platform - Seed Data
-- ============================================================================
-- This file populates the database with demo accounts and sample data
-- for local testing. ALL data here is for testing purposes only.
-- 
-- Passwords (all hashed with PASSWORD_BCRYPT):
--   admin@capitaledge.test   -> Admin@123
--   funder@capitaledge.test  -> Funder@123
--   compliance@capitaledge.test -> Compliance@123
--   finance@capitaledge.test -> Finance@123
--
-- IMPORTANT: These are DEMO accounts. Do not use in any production environment.
-- ============================================================================

SET NAMES utf8mb4;

-- ============================================================================
-- ROLES
-- ============================================================================
INSERT INTO roles (name, display_name, description) VALUES
('admin', 'Administrator', 'Full system access with all administrative privileges'),
('funder', 'Funder', 'Platform user who contributes capital under funding arrangements'),
('compliance', 'Compliance Reviewer', 'Reviews KYC cases and compliance-related matters'),
('finance', 'Finance Operator', 'Manages financial operations, results, and distributions');

-- ============================================================================
-- PERMISSIONS
-- ============================================================================
INSERT INTO permissions (name, display_name, description) VALUES
('manage_users', 'Manage Users', 'Create, edit, suspend user accounts'),
('review_kyc', 'Review KYC', 'Review and approve/reject KYC verification cases'),
('manage_products', 'Manage Products', 'Configure funding products and arrangements'),
('manage_agreements', 'Manage Agreements', 'Create and update funding agreements'),
('review_deposits', 'Review Deposits', 'Review and confirm simulated deposits'),
('record_results', 'Record Financial Results', 'Enter simulated financial results'),
('approve_results', 'Approve Financial Results', 'Approve financial results for distribution'),
('calculate_distributions', 'Calculate Distributions', 'Run distribution calculations'),
('approve_distributions', 'Approve Distributions', 'Approve distributions for payment'),
('process_withdrawals', 'Process Withdrawals', 'Review and process withdrawal requests'),
('view_audit_logs', 'View Audit Logs', 'View system audit logs'),
('view_reports', 'View Reports', 'View financial and operational reports');

-- ============================================================================
-- ROLE-PERMISSION ASSIGNMENTS
-- ============================================================================
-- Admin gets all permissions
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r CROSS JOIN permissions p
WHERE r.name = 'admin';

-- Compliance reviewer permissions
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r CROSS JOIN permissions p
WHERE r.name = 'compliance' AND p.name IN ('review_kyc', 'view_reports');

-- Finance operator permissions
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r CROSS JOIN permissions p
WHERE r.name = 'finance' AND p.name IN (
    'review_deposits', 'record_results', 'approve_results',
    'calculate_distributions', 'approve_distributions',
    'process_withdrawals', 'view_reports'
);

-- ============================================================================
-- DEMO USERS
-- ============================================================================
-- Admin user
INSERT INTO users (id, email, password, first_name, last_name, phone, status, email_verified_at) VALUES
(1, 'admin@capitaledge.test',
 '$2y$10$92IXUNpkjO0qO4rQlGzHxeK5fGJrTMne9DVzn1F3B2MfQO1bRlSyC',
 'System', 'Administrator', '+256700000001', 'active', NOW());

-- Funder user
INSERT INTO users (id, email, password, first_name, last_name, phone, status, email_verified_at) VALUES
(2, 'funder@capitaledge.test',
 '$2y$10$92IXUNpkjO0qO4rQlGzHxeK5fGJrTMne9DVzn1F3B2MfQO1bRlSyC',
 'Jane', 'Mugisha', '+256700000002', 'active', NOW());

-- Compliance reviewer
INSERT INTO users (id, email, password, first_name, last_name, phone, status, email_verified_at) VALUES
(3, 'compliance@capitaledge.test',
 '$2y$10$92IXUNpkjO0qO4rQlGzHxeK5fGJrTMne9DVzn1F3B2MfQO1bRlSyC',
 'David', 'Okello', '+256700000003', 'active', NOW());

-- Finance operator
INSERT INTO users (id, email, password, first_name, last_name, phone, status, email_verified_at) VALUES
(4, 'finance@capitaledge.test',
 '$2y$10$92IXUNpkjO0qO4rQlGzHxeK5fGJrTMne9DVzn1F3B2MfQO1bRlSyC',
 'Sarah', 'Nabirye', '+256700000004', 'active', NOW());

-- NOTE: The passwords above are all the hash for 'Admin@123' for simplicity.
-- After import, run the PHP seed script to generate proper individual password hashes,
-- or use the application's registration flow to create new users.

-- ============================================================================
-- USER-ROLE ASSIGNMENTS
-- ============================================================================
INSERT INTO user_roles (user_id, role_id) VALUES
(1, (SELECT id FROM roles WHERE name = 'admin')),
(2, (SELECT id FROM roles WHERE name = 'funder')),
(3, (SELECT id FROM roles WHERE name = 'compliance')),
(4, (SELECT id FROM roles WHERE name = 'finance'));

-- ============================================================================
-- USER PROFILES
-- ============================================================================
INSERT INTO user_profiles (user_id, date_of_birth, nationality, address, city, country, occupation) VALUES
(1, '1985-01-15', 'Ugandan', '1 Admin Plaza', 'Kampala', 'Uganda', 'Platform Administrator'),
(2, '1990-06-20', 'Ugandan', '15 Kira Road', 'Kampala', 'Uganda', 'Business Owner');

-- ============================================================================
-- FUNDING PRODUCTS
-- ============================================================================
INSERT INTO funding_products (code, name, description, product_type, profit_share_pct, distribution_period, management_fee_pct, entry_fee_pct, min_lock_in_days, withdrawal_notice_days, loss_carry_forward, status, version, version_notes) VALUES
('PROFIT_SHARE_A', 'Profit Sharing Arrangement (Option A)',
 'Under this arrangement, the funder receives an agreed share of eligible realized profits. Distributions are calculated when financial results are finalized and approved. This option provides flexibility with no fixed distribution schedule.',
 'profit_sharing', 60.00, NULL, 2.00, 0.50, 90, 30, 1, 'active', 1, 'Initial version'),

('PERIODIC_DIST_B', 'Profit Sharing With Periodic Distributions (Option B)',
 'Under this arrangement, the funder receives an agreed share of eligible realized profits on a defined distribution schedule. Distributions are calculated monthly based on eligible profits realized during the period. If no eligible profits are realized in a period, no distribution is made for that period.',
 'periodic_distribution', 55.00, 'monthly', 2.50, 0.50, 60, 14, 1, 'active', 1, 'Initial version');

-- ============================================================================
-- FUNDING AGREEMENTS
-- ============================================================================
INSERT INTO funding_agreements (product_id, version, title, body, profit_share_pct, distribution_period, management_fee_pct, entry_fee_pct, min_lock_in_days, loss_carry_forward, effective_from, is_current, created_by) VALUES
(1, 1,
 'Profit Sharing Funding Agreement (Option A) - v1.0',
 '## PROFIT SHARING FUNDING AGREEMENT\n\n**DRAFT - FOR SIMULATION PURPOSES ONLY**\n\nThis agreement is entered into between the Platform Operator and the Funder.\n\n### 1. Definitions\n- "Capital" means the funds contributed by the Funder.\n- "Eligible Realized Profit" means net profits realized after deduction of fees and expenses.\n- "Profit Share" means the percentage of eligible profits allocated to the Funder.\n\n### 2. Profit Sharing Terms\n- Funder''s Profit Share: 60% of eligible realized profits\n- Management Fee: 2% per annum on allocated capital\n- Entry Fee: 0.5% one-time fee on initial capital\n\n### 3. Distribution\n- Distributions are calculated when financial results are finalized.\n- No fixed schedule; distributions occur as results are approved.\n- Losses carry forward and reduce future distributions.\n\n### 4. Withdrawal\n- Minimum lock-in period: 90 days from allocation.\n- Withdrawal notice: 30 days.\n- Capital repayment subject to available balance and agreement terms.\n\n### 5. Risk Disclosure\n- Profits are NOT guaranteed. Capital may be at risk.\n- Past simulated results do not indicate future performance.\n- This is a TEST arrangement with no real money.\n\n### 6. This is a simulated agreement for a university prototype.',
 60.00, NULL, 2.00, 0.50, 90, 1,
 '2026-01-01', 1, 1),

(2, 1,
 'Periodic Distribution Funding Agreement (Option B) - v1.0',
 '## PERIODIC DISTRIBUTION FUNDING AGREEMENT\n\n**DRAFT - FOR SIMULATION PURPOSES ONLY**\n\nThis agreement is entered into between the Platform Operator and the Funder.\n\n### 1. Definitions\n- "Capital" means the funds contributed by the Funder.\n- "Distribution Period" means monthly.\n- "Calculation Date" means the last business day of each period.\n- "Eligible Realized Profit" means net profits realized during the distribution period.\n\n### 2. Profit Sharing Terms\n- Funder''s Profit Share: 55% of eligible realized profits\n- Management Fee: 2.5% per annum on allocated capital\n- Entry Fee: 0.5% one-time fee on initial capital\n\n### 3. Distribution Schedule\n- Distributions calculated monthly on the Calculation Date.\n- If no eligible profits are realized, no distribution for that period.\n- Losses carry forward until offset by subsequent profits.\n\n### 4. Withdrawal\n- Minimum lock-in period: 60 days from allocation.\n- Withdrawal notice: 14 days.\n- Capital repayment subject to available balance and agreement terms.\n\n### 5. Risk Disclosure\n- Profits are NOT guaranteed. Capital may be at risk.\n- Periodic distributions may be zero if no profits are realized.\n- This is a TEST arrangement with no real money.\n\n### 6. This is a simulated agreement for a university prototype.',
 55.00, 'monthly', 2.50, 0.50, 60, 1,
 '2026-01-01', 1, 1);

-- ============================================================================
-- FINANCIAL ACCOUNTS
-- ============================================================================
-- System accounts
INSERT INTO financial_accounts (account_code, account_name, account_type, currency) VALUES
('SYS_DEPOSIT_HOLDING', 'Deposit Holding Account', 'liability', 'UGX'),
('SYS_CAPITAL_POOL', 'Capital Pool', 'equity', 'UGX'),
('SYS_FEE_INCOME', 'Fee Income', 'revenue', 'UGX'),
('SYS_DISTRIBUTION_PAYABLE', 'Distribution Payable', 'liability', 'UGX'),
('SYS_WITHDRAWAL_PENDING', 'Withdrawal Pending', 'liability', 'UGX'),
('SYS_OPERATING', 'Operating Account', 'asset', 'UGX');

-- Funder accounts (for user ID 2)
INSERT INTO financial_accounts (user_id, account_code, account_name, account_type, currency) VALUES
(2, 'USR2_DEPOSIT', 'Funder Deposit Wallet', 'liability', 'UGX'),
(2, 'USR2_CAPITAL', 'Funder Allocated Capital', 'asset', 'UGX'),
(2, 'USR2_DISTRIBUTION', 'Funder Distribution Balance', 'liability', 'UGX'),
(2, 'USR2_WITHDRAWABLE', 'Funder Withdrawable Balance', 'liability', 'UGX');
