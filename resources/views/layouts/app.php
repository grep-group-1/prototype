<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Escape::html($pageTitle ?? 'Dashboard') ?> | CapitalEdge Funding</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= Url::asset('css/style.css') ?>" rel="stylesheet">
</head>
<body>

<!-- Test Mode Banner -->
<div class="ce-test-banner">
    <i class="bi bi-exclamation-triangle-fill me-1"></i>
    TEST MODE &mdash; Simulated Platform. No real funds or transactions.
</div>

<!-- Sidebar -->
<aside class="ce-sidebar" id="sidebar">
    <div class="ce-sidebar-brand">
        <a href="<?= Url::base() ?>">
            Capital<span class="brand-accent">Edge</span>
        </a>
    </div>
    <nav class="ce-sidebar-nav">
        <?php if ($auth->isFunder()): ?>
            <!-- Funder Navigation -->
            <div class="nav-section">Main</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= ($_SERVER['REQUEST_URI'] ?? '') && str_contains($_SERVER['REQUEST_URI'], '/dashboard') ? 'active' : '' ?>" 
                       href="<?= Url::to('dashboard') ?>">
                        <i class="bi bi-grid-1x2-fill"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('profile') ?>">
                        <i class="bi bi-person"></i> My Profile
                    </a>
                </li>
            </ul>
            <div class="nav-section">Funding</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('kyc') ?>">
                        <i class="bi bi-shield-check"></i> Identity Verification
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('agreements') ?>">
                        <i class="bi bi-file-earmark-text"></i> Agreements
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('deposits') ?>">
                        <i class="bi bi-plus-circle"></i> Deposits
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('withdrawals') ?>">
                        <i class="bi bi-arrow-up-circle"></i> Withdrawals
                    </a>
                </li>
            </ul>
            <div class="nav-section">Records</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('transactions') ?>">
                        <i class="bi bi-clock-history"></i> Transactions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('statements') ?>">
                        <i class="bi bi-download"></i> Statements
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('notifications') ?>">
                        <i class="bi bi-bell"></i> Notifications
                    </a>
                </li>
            </ul>

        <?php elseif ($auth->hasAnyRole(['admin', 'compliance', 'finance'])): ?>
            <!-- Admin Navigation -->
            <div class="nav-section">Overview</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('admin') ?>">
                        <i class="bi bi-grid-1x2-fill"></i> Dashboard
                    </a>
                </li>
            </ul>
            <div class="nav-section">Management</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('admin/users') ?>">
                        <i class="bi bi-people"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('admin/kyc') ?>">
                        <i class="bi bi-shield-check"></i> KYC Review
                    </a>
                </li>
                <?php if ($auth->isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('admin/products') ?>">
                        <i class="bi bi-box-seam"></i> Products
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            <div class="nav-section">Finance</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('admin/deposits') ?>">
                        <i class="bi bi-plus-circle"></i> Deposits
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('admin/results') ?>">
                        <i class="bi bi-graph-up"></i> Financial Results
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('admin/distributions') ?>">
                        <i class="bi bi-cash-coin"></i> Distributions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('admin/withdrawals') ?>">
                        <i class="bi bi-arrow-up-circle"></i> Withdrawals
                    </a>
                </li>
            </ul>
            <div class="nav-section">Reports</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('admin/reports') ?>">
                        <i class="bi bi-bar-chart"></i> Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('admin/reports/ledger') ?>">
                        <i class="bi bi-journal-text"></i> Ledger
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('admin/reports/reconciliation') ?>">
                        <i class="bi bi-check2-circle"></i> Reconciliation
                    </a>
                </li>
                <?php if ($auth->isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('admin/audit') ?>">
                        <i class="bi bi-shield-lock"></i> Audit Logs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('admin/settings') ?>">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>

        <div class="nav-section">Account</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="<?= Url::to('profile') ?>">
                    <i class="bi bi-person-gear"></i> Profile Settings
                </a>
            </li>
            <li class="nav-item">
                <form method="POST" action="<?= Url::to('logout') ?>" class="d-inline">
                    <?= Csrf::field() ?>
                    <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start" style="cursor: pointer;">
                        <i class="bi bi-box-arrow-left"></i> Log Out
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</aside>

<!-- Main Content -->
<div class="ce-main">
    <!-- Top Bar -->
    <div class="ce-topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="page-title"><?= Escape::html($pageTitle ?? 'Dashboard') ?></h1>
        </div>
        <div class="d-flex align-items-center gap-3">
            <!-- Notifications -->
            <div class="position-relative">
                <a href="<?= Url::to('notifications') ?>" class="text-muted">
                    <i class="bi bi-bell fs-5"></i>
                    <?php
                    $notifCount = $currentUser ? \App\Services\Database::getInstance()->fetchOne(
                        "SELECT COUNT(*) as cnt FROM notifications WHERE user_id = ? AND is_read = 0",
                        [$currentUser['id']]
                    ) : null;
                    if ($notifCount && $notifCount['cnt'] > 0):
                    ?>
                    <span class="ce-notification-badge"><?= (int)$notifCount['cnt'] ?></span>
                    <?php endif; ?>
                </a>
            </div>
            <!-- User Menu -->
            <div class="dropdown">
                <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    <div class="rounded-circle bg-navy text-white d-flex align-items-center justify-content-center me-2" 
                         style="width:32px;height:32px;font-size:0.8rem;font-weight:600;">
                        <?= strtoupper(substr($currentUser['first_name'] ?? 'U', 0, 1)) ?><?= strtoupper(substr($currentUser['last_name'] ?? '', 0, 1)) ?>
                    </div>
                    <span class="d-none d-md-inline fs-sm fw-600 text-navy">
                        <?= Escape::html(($currentUser['first_name'] ?? '') . ' ' . ($currentUser['last_name'] ?? '')) ?>
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><a class="dropdown-item" href="<?= Url::to('profile') ?>"><i class="bi bi-person me-2"></i>Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="<?= Url::to('logout') ?>">
                            <?= Csrf::field() ?>
                            <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-left me-2"></i>Log Out</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (!empty($flashMessages)): ?>
    <div class="ce-content pb-0">
        <?php foreach ($flashMessages as $msg): ?>
            <div class="alert alert-<?= Escape::html($msg['type']) ?> alert-dismissible fade show ce-alert ce-alert-<?= Escape::html($msg['type']) ?>" role="alert">
                <?= Escape::html($msg['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Page Content -->
    <div class="ce-content">
        <?= $content ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="<?= Url::asset('js/app.js') ?>"></script>
</body>
</html>
