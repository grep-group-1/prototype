<!-- Admin Dashboard -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="ce-stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Active Users</div>
                    <div class="stat-value"><?= number_format($totalUsers) ?></div>
                </div>
                <div class="stat-icon bg-info"><i class="bi bi-people"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="ce-stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Pending KYC</div>
                    <div class="stat-value"><?= number_format($pendingKyc) ?></div>
                </div>
                <div class="stat-icon bg-gold"><i class="bi bi-shield-exclamation"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="ce-stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Deposits</div>
                    <div class="stat-value"><?= Format::currency($totalDeposits) ?></div>
                </div>
                <div class="stat-icon bg-success"><i class="bi bi-plus-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="ce-stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Allocated</div>
                    <div class="stat-value"><?= Format::currency($totalAllocated) ?></div>
                </div>
                <div class="stat-icon bg-navy"><i class="bi bi-pie-chart"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Action Required -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="ce-card border-start border-3 border-warning">
            <div class="ce-card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-600 mb-1">Pending KYC Reviews</h6>
                    <p class="fs-sm text-muted-ce mb-0"><?= number_format($pendingKyc) ?> cases awaiting review</p>
                </div>
                <a href="<?= Url::to('admin/kyc') ?>" class="btn btn-sm btn-ce-outline">Review</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="ce-card border-start border-3 border-info">
            <div class="ce-card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-600 mb-1">Pending Withdrawals</h6>
                    <p class="fs-sm text-muted-ce mb-0"><?= number_format($pendingWithdrawals) ?> requests to process</p>
                </div>
                <a href="<?= Url::to('admin/withdrawals') ?>" class="btn btn-sm btn-ce-outline">Process</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="ce-card border-start border-3 border-success">
            <div class="ce-card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-600 mb-1">Results Pending Review</h6>
                    <p class="fs-sm text-muted-ce mb-0"><?= number_format($pendingResults) ?> results awaiting approval</p>
                </div>
                <a href="<?= Url::to('admin/results') ?>" class="btn btn-sm btn-ce-outline">Review</a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="ce-card">
    <div class="ce-card-header d-flex justify-content-between align-items-center">
        <span>Recent Activity</span>
        <?php if ($auth->isAdmin()): ?>
        <a href="<?= Url::to('admin/audit') ?>" class="fs-sm">View All Logs</a>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <?php if (empty($recentActivity)): ?>
            <div class="ce-empty-state">
                <i class="bi bi-clock-history"></i>
                <h5>No activity yet</h5>
                <p class="fs-sm">System activity will appear here as actions are performed.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="ce-table mb-0">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>User</th>
                            <th>Entity</th>
                            <th>Description</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentActivity as $log): ?>
                        <tr>
                            <td><span class="ce-badge ce-badge-navy"><?= Escape::html($log['action']) ?></span></td>
                            <td class="fs-sm"><?= Escape::html(($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? 'System')) ?></td>
                            <td class="fs-sm text-muted-ce"><?= Escape::html($log['entity_type'] ?? '—') ?> #<?= $log['entity_id'] ?? '' ?></td>
                            <td class="fs-sm text-muted-ce"><?= Escape::html(Format::truncate($log['description'] ?? '', 50)) ?></td>
                            <td class="fs-sm text-muted-ce"><?= Format::timeAgo($log['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
