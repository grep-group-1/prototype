<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="<?= Url::to('deposits/new') ?>" class="btn btn-ce-primary">
            <i class="bi bi-plus-circle me-1"></i>New Deposit
        </a>
    </div>
</div>
<div class="ce-card">
    <div class="card-body p-0">
        <?php if (empty($deposits)): ?>
            <div class="ce-empty-state">
                <i class="bi bi-plus-circle"></i>
                <h5>No deposits yet</h5>
                <p class="fs-sm">Make your first simulated deposit to get started.</p>
                <a href="<?= Url::to('deposits/new') ?>" class="btn-ce-primary">Make a Deposit</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="ce-table mb-0">
                    <thead>
                        <tr><th>Reference</th><th>Amount</th><th>Status</th><th>Date</th><th></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($deposits as $dep): ?>
                        <tr>
                            <td class="fw-600"><?= Escape::html($dep['reference']) ?></td>
                            <td class="fw-600"><?= Format::currency($dep['amount']) ?></td>
                            <td>
                                <?php $badgeClass = match($dep['status']) { 'completed' => 'success', 'pending' => 'warning', 'failed' => 'danger', default => 'muted' }; ?>
                                <span class="ce-badge ce-badge-<?= $badgeClass ?>"><?= ucfirst($dep['status']) ?></span>
                            </td>
                            <td class="fs-sm text-muted-ce"><?= Format::dateTime($dep['created_at']) ?></td>
                            <td><a href="<?= Url::to("deposits/{$dep['id']}") ?>" class="fs-sm">View</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
