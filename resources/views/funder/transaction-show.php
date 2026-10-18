<div class="ce-card">
    <div class="ce-card-header">Transaction Details</div>
    <div class="ce-card-body">
        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="fs-sm text-muted-ce">Reference</div><div class="fw-600"><?= Escape::html($journal['reference']) ?></div></div>
            <div class="col-md-4"><div class="fs-sm text-muted-ce">Type</div><span class="ce-badge ce-badge-navy"><?= ucfirst(str_replace('_', ' ', $journal['journal_type'])) ?></span></div>
            <div class="col-md-4"><div class="fs-sm text-muted-ce">Date</div><div><?= Format::dateTime($journal['created_at']) ?></div></div>
            <div class="col-md-8"><div class="fs-sm text-muted-ce">Description</div><div><?= Escape::html($journal['description']) ?></div></div>
            <div class="col-md-4"><div class="fs-sm text-muted-ce">Total Amount</div><div class="fw-600 fs-5 text-navy"><?= Format::currency($journal['total_amount']) ?></div></div>
        </div>
        <h6 class="fw-600 mb-3">Ledger Entries</h6>
        <div class="table-responsive">
            <table class="ce-table">
                <thead><tr><th>Account</th><th>Type</th><th class="text-end">Amount</th></tr></thead>
                <tbody>
                    <?php foreach ($entries as $entry): ?>
                    <tr>
                        <td><div class="fw-600"><?= Escape::html($entry['account_name']) ?></div><div class="fs-sm text-muted-ce"><?= Escape::html($entry['account_code']) ?></div></td>
                        <td><span class="ce-badge ce-badge-<?= $entry['entry_type'] === 'debit' ? 'info' : 'success' ?>"><?= ucfirst($entry['entry_type']) ?></span></td>
                        <td class="text-end fw-600"><?= Format::currency($entry['amount']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
