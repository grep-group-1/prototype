<!-- Funder Dashboard -->
<div class="row g-4 mb-4">
    <!-- Balance Cards -->
    <div class="col-md-6 col-xl-3">
        <div class="ce-stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Confirmed Deposits</div>
                    <div class="stat-value"><?= Format::currency($balances['USR' . ($currentUser['id'] ?? '0') . '_DEPOSIT'] ?? '0') ?></div>
                </div>
                <div class="stat-icon bg-navy"><i class="bi bi-wallet2"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="ce-stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Allocated Capital</div>
                    <div class="stat-value"><?= Format::currency($balances['USR' . ($currentUser['id'] ?? '0') . '_CAPITAL'] ?? '0') ?></div>
                </div>
                <div class="stat-icon bg-gold"><i class="bi bi-pie-chart"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="ce-stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Withdrawable Balance</div>
                    <div class="stat-value"><?= Format::currency($balances['USR' . ($currentUser['id'] ?? '0') . '_WITHDRAWABLE'] ?? '0') ?></div>
                </div>
                <div class="stat-icon bg-success"><i class="bi bi-arrow-up-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="ce-stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Distributions Earned</div>
                    <div class="stat-value"><?= Format::currency($balances['USR' . ($currentUser['id'] ?? '0') . '_DISTRIBUTION'] ?? '0') ?></div>
                </div>
                <div class="stat-icon bg-info"><i class="bi bi-graph-up"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column -->
    <div class="col-lg-8">
        <!-- Status Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="ce-card">
                    <div class="ce-card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-shield-check <?= $kycStatus === 'approved' ? 'text-success' : 'text-warning' ?>"></i>
                            <span class="fs-sm fw-600">KYC Status</span>
                        </div>
                        <span class="ce-badge ce-badge-<?= $kycStatus === 'approved' ? 'success' : ($kycStatus === 'pending' ? 'warning' : 'muted') ?>">
                            <?= ucfirst(str_replace('_', ' ', $kycStatus)) ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="ce-card">
                    <div class="ce-card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-file-earmark-text <?= $activeAgreement ? 'text-success' : 'text-warning' ?>"></i>
                            <span class="fs-sm fw-600">Agreement</span>
                        </div>
                        <?php if ($activeAgreement): ?>
                            <span class="ce-badge ce-badge-success">Active</span>
                            <div class="fs-sm text-muted-ce mt-1"><?= Escape::html(Format::truncate($activeAgreement['product_name'], 30)) ?></div>
                        <?php else: ?>
                            <span class="ce-badge ce-badge-warning">None</span>
                            <div class="fs-sm mt-1"><a href="<?= Url::to('agreements') ?>">Select arrangement</a></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="ce-card">
                    <div class="ce-card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-arrow-up-circle text-info"></i>
                            <span class="fs-sm fw-600">Pending Withdrawals</span>
                        </div>
                        <span class="ce-badge ce-badge-<?= count($pendingWithdrawals) > 0 ? 'info' : 'muted' ?>">
                            <?= count($pendingWithdrawals) ?> pending
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="ce-card mb-4">
            <div class="ce-card-header d-flex justify-content-between align-items-center">
                <span>Recent Transactions</span>
                <a href="<?= Url::to('transactions') ?>" class="fs-sm">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentTransactions)): ?>
                    <div class="ce-empty-state">
                        <i class="bi bi-inbox"></i>
                        <h5>No transactions yet</h5>
                        <p class="fs-sm">Your transaction history will appear here after your first deposit.</p>
                        <a href="<?= Url::to('deposits/new') ?>" class="btn-ce-primary btn-sm">Make a Deposit</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="ce-table mb-0">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th class="text-end">Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentTransactions as $txn): ?>
                                <tr>
                                    <td><a href="<?= Url::to("transactions/{$txn['reference']}") ?>" class="fw-600"><?= Escape::html($txn['reference']) ?></a></td>
                                    <td><span class="ce-badge ce-badge-navy"><?= Escape::html(ucfirst(str_replace('_', ' ', $txn['journal_type']))) ?></span></td>
                                    <td class="text-muted-ce"><?= Escape::html(Format::truncate($txn['description'], 40)) ?></td>
                                    <td class="text-end fw-600"><?= Format::currency($txn['total_amount']) ?></td>
                                    <td class="text-muted-ce fs-sm"><?= Format::date($txn['created_at']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Notifications -->
        <div class="ce-card">
            <div class="ce-card-header d-flex justify-content-between align-items-center">
                <span>Notifications</span>
                <a href="<?= Url::to('notifications') ?>" class="fs-sm">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentNotifications)): ?>
                    <div class="ce-empty-state">
                        <i class="bi bi-bell-slash"></i>
                        <h5>No notifications</h5>
                        <p class="fs-sm">You're all caught up.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentNotifications as $notif): ?>
                        <div class="list-group-item border-0 px-4 py-3 <?= $notif['is_read'] ? '' : 'bg-light' ?>">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="fw-600 fs-sm"><?= Escape::html($notif['title']) ?></span>
                                    <p class="mb-0 fs-sm text-muted-ce"><?= Escape::html(Format::truncate($notif['message'], 80)) ?></p>
                                </div>
                                <span class="fs-sm text-muted-ce"><?= Format::timeAgo($notif['created_at']) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="ce-card mb-4">
            <div class="ce-card-header">Quick Actions</div>
            <div class="ce-card-body">
                <div class="d-grid gap-2">
                    <a href="<?= Url::to('deposits/new') ?>" class="btn btn-ce-primary">
                        <i class="bi bi-plus-circle me-2"></i>Make a Deposit
                    </a>
                    <a href="<?= Url::to('withdrawals/new') ?>" class="btn btn-ce-outline">
                        <i class="bi bi-arrow-up-circle me-2"></i>Request Withdrawal
                    </a>
                    <a href="<?= Url::to('statements') ?>" class="btn btn-ce-outline">
                        <i class="bi bi-download me-2"></i>Download Statement
                    </a>
                </div>
            </div>
        </div>

        <!-- Active Agreement Details -->
        <?php if ($activeAgreement): ?>
        <div class="ce-card mb-4">
            <div class="ce-card-header">Active Agreement</div>
            <div class="ce-card-body">
                <h6 class="fw-600"><?= Escape::html($activeAgreement['product_name']) ?></h6>
                <div class="fs-sm text-muted-ce mb-3">
                    Code: <?= Escape::html($activeAgreement['product_code']) ?>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="fs-sm text-muted-ce">Profit Share</span>
                    <span class="fs-sm fw-600"><?= Format::percentage($activeAgreement['profit_share_pct']) ?></span>
                </div>
                <a href="<?= Url::to('agreements') ?>" class="fs-sm">View Details</a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Capital Allocations -->
        <?php if (!empty($allocations)): ?>
        <div class="ce-card mb-4">
            <div class="ce-card-header">Capital Allocations</div>
            <div class="ce-card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($allocations as $alloc): ?>
                    <div class="list-group-item border-0 px-3 py-2">
                        <div class="d-flex justify-content-between">
                            <span class="fs-sm fw-600"><?= Format::currency($alloc['amount']) ?></span>
                            <span class="ce-badge ce-badge-success">Allocated</span>
                        </div>
                        <span class="fs-sm text-muted-ce"><?= Format::date($alloc['allocated_at']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
