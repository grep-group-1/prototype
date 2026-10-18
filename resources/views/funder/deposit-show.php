<div class="row">
    <div class="col-lg-8">
        <div class="ce-card mb-4">
            <div class="ce-card-header">Deposit Details</div>
            <div class="ce-card-body">
                <div class="row g-3">
                    <div class="col-md-6"><div class="fs-sm text-muted-ce">Reference</div><div class="fw-600"><?= Escape::html($transaction['reference']) ?></div></div>
                    <div class="col-md-6"><div class="fs-sm text-muted-ce">Amount</div><div class="fw-600 fs-4 text-navy"><?= Format::currency($transaction['amount']) ?></div></div>
                    <div class="col-md-6"><div class="fs-sm text-muted-ce">Status</div>
                        <span class="ce-badge ce-badge-<?= $transaction['status'] === 'completed' ? 'success' : 'warning' ?>"><?= ucfirst($transaction['status']) ?></span>
                    </div>
                    <div class="col-md-6"><div class="fs-sm text-muted-ce">Date</div><div><?= Format::dateTime($transaction['created_at']) ?></div></div>
                    <div class="col-md-6"><div class="fs-sm text-muted-ce">Gateway Reference</div><div><?= Escape::html($transaction['gateway_reference'] ?? '—') ?></div></div>
                    <div class="col-md-6"><div class="fs-sm text-muted-ce">Payment Method</div><div><?= Escape::html(ucfirst(str_replace('_', ' ', $transaction['payment_method']))) ?></div></div>
                </div>
            </div>
        </div>
        <?php if (!empty($ledgerEntries)): ?>
        <div class="ce-card">
            <div class="ce-card-header">Ledger Entries</div>
            <div class="card-body p-0">
                <table class="ce-table mb-0">
                    <thead><tr><th>Reference</th><th>Description</th><th>Type</th><th class="text-end">Amount</th></tr></thead>
                    <tbody>
                        <?php foreach ($ledgerEntries as $entry): ?>
                        <tr>
                            <td class="fw-600"><?= Escape::html($entry['reference']) ?></td>
                            <td class="fs-sm"><?= Escape::html($entry['description']) ?></td>
                            <td><span class="ce-badge ce-badge-<?= $entry['entry_type'] === 'debit' ? 'info' : 'navy' ?>"><?= ucfirst($entry['entry_type']) ?></span></td>
                            <td class="text-end fw-600"><?= Format::currency($entry['entry_amount']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div class="col-lg-4">
        <a href="<?= Url::to('deposits') ?>" class="btn btn-ce-outline w-100"><i class="bi bi-arrow-left me-2"></i>Back to Deposits</a>
    </div>
</div>
