<div class="ce-card mb-4">
    <div class="ce-card-body">
        <form method="GET" action="<?= Url::to('transactions') ?>" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="ce-form-label">Type</label>
                <select class="form-control ce-form-control" name="type">
                    <option value="">All Types</option>
                    <option value="test_deposit" <?= ($filters['type'] ?? '') === 'test_deposit' ? 'selected' : '' ?>>Deposit</option>
                    <option value="capital_allocation" <?= ($filters['type'] ?? '') === 'capital_allocation' ? 'selected' : '' ?>>Capital Allocation</option>
                    <option value="distribution_accrual" <?= ($filters['type'] ?? '') === 'distribution_accrual' ? 'selected' : '' ?>>Distribution</option>
                    <option value="withdrawal_request" <?= ($filters['type'] ?? '') === 'withdrawal_request' ? 'selected' : '' ?>>Withdrawal</option>
                    <option value="fee" <?= ($filters['type'] ?? '') === 'fee' ? 'selected' : '' ?>>Fee</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="ce-form-label">From</label>
                <input type="date" class="form-control ce-form-control" name="date_from" value="<?= Escape::html($filters['date_from'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="ce-form-label">To</label>
                <input type="date" class="form-control ce-form-control" name="date_to" value="<?= Escape::html($filters['date_to'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-ce-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>
<div class="ce-card">
    <div class="ce-card-header">
        <span>Transactions</span>
        <span class="fs-sm text-muted-ce ms-2">(<?= $total ?> total)</span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($transactions)): ?>
            <div class="ce-empty-state"><i class="bi bi-clock-history"></i><h5>No transactions found</h5></div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="ce-table mb-0">
                    <thead><tr><th>Date</th><th>Reference</th><th>Type</th><th>Description</th><th class="text-end">Amount</th></tr></thead>
                    <tbody>
                        <?php foreach ($transactions as $txn): ?>
                        <tr>
                            <td class="fs-sm text-muted-ce"><?= Format::dateTime($txn['created_at']) ?></td>
                            <td><a href="<?= Url::to("transactions/{$txn['reference']}") ?>" class="fw-600"><?= Escape::html($txn['reference']) ?></a></td>
                            <td><span class="ce-badge ce-badge-navy"><?= Escape::html(ucfirst(str_replace('_', ' ', $txn['journal_type']))) ?></span></td>
                            <td class="fs-sm text-muted-ce"><?= Escape::html(Format::truncate($txn['description'], 50)) ?></td>
                            <td class="text-end fw-600"><?= Format::currency($txn['total_amount']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($totalPages > 1): ?>
            <div class="ce-card-body d-flex justify-content-center">
                <nav><ul class="pagination ce-pagination mb-0">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= Url::to('transactions', array_merge($filters, ['page' => $p])) ?>"><?= $p ?></a>
                    </li>
                    <?php endfor; ?>
                </ul></nav>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
