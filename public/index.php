<?php
/**
 * Front Controller
 * 
 * All HTTP requests are routed through this file via .htaccess rewrite rules.
 * This initializes the application and dispatches the request to the router.
 */

// Bootstrap the application
require_once __DIR__ . '/../app/bootstrap.php';

use App\Services\Router;
use App\Helpers\Url;

// Initialize the router
$router = new Router();

// ============================================================================
// PUBLIC ROUTES (no authentication required)
// ============================================================================
$router->get('/', [\App\Controllers\PublicController::class, 'home'], 'home');
$router->get('/about', [\App\Controllers\PublicController::class, 'about'], 'about');
$router->get('/how-it-works', [\App\Controllers\PublicController::class, 'howItWorks'], 'how-it-works');
$router->get('/terms', [\App\Controllers\PublicController::class, 'terms'], 'terms');
$router->get('/privacy', [\App\Controllers\PublicController::class, 'privacy'], 'privacy');
$router->get('/risk-disclosure', [\App\Controllers\PublicController::class, 'riskDisclosure'], 'risk-disclosure');
$router->get('/faq', [\App\Controllers\PublicController::class, 'faq'], 'faq');
$router->get('/contact', [\App\Controllers\PublicController::class, 'contact'], 'contact');

// ============================================================================
// AUTHENTICATION ROUTES (guest only)
// ============================================================================
$router->get('/login', [\App\Controllers\AuthController::class, 'showLogin'], 'login', ['guest']);
$router->post('/login', [\App\Controllers\AuthController::class, 'login'], 'login.post', ['guest']);
$router->get('/register', [\App\Controllers\AuthController::class, 'showRegister'], 'register', ['guest']);
$router->post('/register', [\App\Controllers\AuthController::class, 'register'], 'register.post', ['guest']);
$router->post('/logout', [\App\Controllers\AuthController::class, 'logout'], 'logout');
$router->get('/logout', [\App\Controllers\AuthController::class, 'logout'], 'logout.get');
$router->get('/forgot-password', [\App\Controllers\AuthController::class, 'showForgotPassword'], 'forgot-password', ['guest']);
$router->post('/forgot-password', [\App\Controllers\AuthController::class, 'forgotPassword'], 'forgot-password.post', ['guest']);
$router->get('/reset-password/{token}', [\App\Controllers\AuthController::class, 'showResetPassword'], 'reset-password', ['guest']);
$router->post('/reset-password/{token}', [\App\Controllers\AuthController::class, 'resetPassword'], 'reset-password.post', ['guest']);
$router->get('/verify-email/{token}', [\App\Controllers\AuthController::class, 'verifyEmail'], 'verify-email');

// ============================================================================
// FUNDER ROUTES (authenticated funder)
// ============================================================================
$router->get('/dashboard', [\App\Controllers\DashboardController::class, 'index'], 'dashboard', ['auth', 'funder']);
$router->get('/profile', [\App\Controllers\ProfileController::class, 'show'], 'profile', ['auth']);
$router->post('/profile', [\App\Controllers\ProfileController::class, 'update'], 'profile.update', ['auth']);

// KYC
$router->get('/kyc', [\App\Controllers\KycController::class, 'show'], 'kyc', ['auth', 'funder']);
$router->post('/kyc/submit', [\App\Controllers\KycController::class, 'submit'], 'kyc.submit', ['auth', 'funder']);

// Agreements
$router->get('/agreements', [\App\Controllers\AgreementController::class, 'index'], 'agreements', ['auth', 'funder']);
$router->get('/agreements/{id}', [\App\Controllers\AgreementController::class, 'show'], 'agreement.show', ['auth', 'funder']);
$router->post('/agreements/{id}/accept', [\App\Controllers\AgreementController::class, 'accept'], 'agreement.accept', ['auth', 'funder']);

// Deposits
$router->get('/deposits', [\App\Controllers\DepositController::class, 'index'], 'deposits', ['auth', 'funder']);
$router->get('/deposits/new', [\App\Controllers\DepositController::class, 'create'], 'deposits.create', ['auth', 'funder']);
$router->post('/deposits', [\App\Controllers\DepositController::class, 'store'], 'deposits.store', ['auth', 'funder']);
$router->get('/deposits/{id}', [\App\Controllers\DepositController::class, 'show'], 'deposits.show', ['auth', 'funder']);

// Withdrawals
$router->get('/withdrawals', [\App\Controllers\WithdrawalController::class, 'index'], 'withdrawals', ['auth', 'funder']);
$router->get('/withdrawals/new', [\App\Controllers\WithdrawalController::class, 'create'], 'withdrawals.create', ['auth', 'funder']);
$router->post('/withdrawals', [\App\Controllers\WithdrawalController::class, 'store'], 'withdrawals.store', ['auth', 'funder']);
$router->get('/withdrawals/{id}', [\App\Controllers\WithdrawalController::class, 'show'], 'withdrawals.show', ['auth', 'funder']);

// Transactions
$router->get('/transactions', [\App\Controllers\TransactionController::class, 'index'], 'transactions', ['auth', 'funder']);
$router->get('/transactions/{reference}', [\App\Controllers\TransactionController::class, 'show'], 'transactions.show', ['auth', 'funder']);

// Statements
$router->get('/statements', [\App\Controllers\StatementController::class, 'index'], 'statements', ['auth', 'funder']);
$router->get('/statements/download', [\App\Controllers\StatementController::class, 'download'], 'statements.download', ['auth', 'funder']);

// Notifications
$router->get('/notifications', [\App\Controllers\NotificationController::class, 'index'], 'notifications', ['auth']);
$router->post('/notifications/{id}/read', [\App\Controllers\NotificationController::class, 'markRead'], 'notifications.read', ['auth']);
$router->post('/notifications/read-all', [\App\Controllers\NotificationController::class, 'markAllRead'], 'notifications.read-all', ['auth']);

// ============================================================================
// ADMIN ROUTES (authenticated admin/staff)
// ============================================================================
$router->get('/admin', [\App\Controllers\Admin\AdminController::class, 'index'], 'admin.dashboard', ['auth', 'staff']);
$router->get('/admin/users', [\App\Controllers\Admin\UserController::class, 'index'], 'admin.users', ['auth', 'staff']);
$router->get('/admin/users/{id}', [\App\Controllers\Admin\UserController::class, 'show'], 'admin.users.show', ['auth', 'staff']);
$router->post('/admin/users/{id}/status', [\App\Controllers\Admin\UserController::class, 'updateStatus'], 'admin.users.status', ['auth', 'admin']);

// Admin KYC
$router->get('/admin/kyc', [\App\Controllers\Admin\KycReviewController::class, 'index'], 'admin.kyc', ['auth', 'staff']);
$router->get('/admin/kyc/{id}', [\App\Controllers\Admin\KycReviewController::class, 'show'], 'admin.kyc.show', ['auth', 'staff']);
$router->post('/admin/kyc/{id}/review', [\App\Controllers\Admin\KycReviewController::class, 'review'], 'admin.kyc.review', ['auth', 'staff']);

// Admin Products
$router->get('/admin/products', [\App\Controllers\Admin\ProductController::class, 'index'], 'admin.products', ['auth', 'admin']);
$router->get('/admin/products/create', [\App\Controllers\Admin\ProductController::class, 'create'], 'admin.products.create', ['auth', 'admin']);
$router->post('/admin/products', [\App\Controllers\Admin\ProductController::class, 'store'], 'admin.products.store', ['auth', 'admin']);
$router->get('/admin/products/{id}/edit', [\App\Controllers\Admin\ProductController::class, 'edit'], 'admin.products.edit', ['auth', 'admin']);
$router->post('/admin/products/{id}', [\App\Controllers\Admin\ProductController::class, 'update'], 'admin.products.update', ['auth', 'admin']);

// Admin Deposits
$router->get('/admin/deposits', [\App\Controllers\Admin\DepositReviewController::class, 'index'], 'admin.deposits', ['auth', 'staff']);
$router->post('/admin/deposits/{id}/confirm', [\App\Controllers\Admin\DepositReviewController::class, 'confirm'], 'admin.deposits.confirm', ['auth', 'finance']);

// Admin Financial Results
$router->get('/admin/results', [\App\Controllers\Admin\ResultController::class, 'index'], 'admin.results', ['auth', 'staff']);
$router->get('/admin/results/create', [\App\Controllers\Admin\ResultController::class, 'create'], 'admin.results.create', ['auth', 'finance']);
$router->post('/admin/results', [\App\Controllers\Admin\ResultController::class, 'store'], 'admin.results.store', ['auth', 'finance']);
$router->post('/admin/results/{id}/approve', [\App\Controllers\Admin\ResultController::class, 'approve'], 'admin.results.approve', ['auth', 'admin']);

// Admin Distributions
$router->get('/admin/distributions', [\App\Controllers\Admin\DistributionController::class, 'index'], 'admin.distributions', ['auth', 'staff']);
$router->post('/admin/distributions/calculate', [\App\Controllers\Admin\DistributionController::class, 'calculate'], 'admin.distributions.calculate', ['auth', 'finance']);
$router->post('/admin/distributions/{id}/approve', [\App\Controllers\Admin\DistributionController::class, 'approve'], 'admin.distributions.approve', ['auth', 'admin']);

// Admin Withdrawals
$router->get('/admin/withdrawals', [\App\Controllers\Admin\WithdrawalReviewController::class, 'index'], 'admin.withdrawals', ['auth', 'staff']);
$router->post('/admin/withdrawals/{id}/process', [\App\Controllers\Admin\WithdrawalReviewController::class, 'process'], 'admin.withdrawals.process', ['auth', 'finance']);

// Admin Reports
$router->get('/admin/reports', [\App\Controllers\Admin\ReportController::class, 'index'], 'admin.reports', ['auth', 'staff']);
$router->get('/admin/reports/ledger', [\App\Controllers\Admin\ReportController::class, 'ledger'], 'admin.reports.ledger', ['auth', 'staff']);
$router->get('/admin/reports/reconciliation', [\App\Controllers\Admin\ReportController::class, 'reconciliation'], 'admin.reports.reconciliation', ['auth', 'staff']);

// Admin Audit Logs
$router->get('/admin/audit', [\App\Controllers\Admin\AuditController::class, 'index'], 'admin.audit', ['auth', 'admin']);

// Admin Settings
$router->get('/admin/settings', [\App\Controllers\Admin\SettingsController::class, 'index'], 'admin.settings', ['auth', 'admin']);
$router->post('/admin/settings', [\App\Controllers\Admin\SettingsController::class, 'update'], 'admin.settings.update', ['auth', 'admin']);

// ============================================================================
// DISPATCH THE REQUEST
// ============================================================================
$router->dispatch();
