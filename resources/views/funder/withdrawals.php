<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <div class="ce-stat-card d-inline-block me-3">
            <span class="stat-label">Withdrawable Balance</span>
            <div class="stat-value"><?= Format::currency($withdrawableBalance) ?></div>
        </div>
    </div>
    <a href="<?= Url::to('withdrawals/new') ?>" class="btn btn-ce-primary"><i class="bi bi-arrow-up-circle me-1"></i>Request Withdrawal</a>
</div>
<div class="ce-card">
    <div class="card-body p-0">
        <?php if (empty($withdrawals)): ?>
            <div class="ce-empty-state">
                <i class="bi bi-arrow-up-circle"></i><h5>No withdrawal requests</h5>
                <p class="fs-sm">Request a withdrawal when you have an eligible balance.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="ce-table mb-0">
                    <thead><tr><th>Reference</th><th>Amount</th><th>Status</th><th>Date</th><th></th></tr></thead>
                    <tbody>
                        <?php foreach ($withdrawals as $wd): ?>
                        <tr>
                            <td class="fw-600"><?= Escape::html($wd['reference']) ?></td>
                            <td class="fw-600"><?= Format::currency($wd['amount']) ?></td>
                            <td>
                                <?php $badgeClass = match($wd['status']) { 'completed' => 'success', 'pending' => 'warning', 'approved' => 'info', 'rejected' => 'danger', default => 'muted' }; ?>
                                <span class="ce-badge ce-badge-<?= $badgeClass ?>"><?= ucfirst($wd['status']) ?></span>
                            </td>
                            <td class="fs-sm text-muted-ce"><?= Format::dateTime($wd['created_at']) ?></td>
                            <td><a href="<?= Url::to("withdrawals/{$wd['id']}") ?>" class="fs-sm">View</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
