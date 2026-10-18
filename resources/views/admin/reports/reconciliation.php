<div class="ce-card mb-4">
    <div class="ce-card-header">Double-Entry Balance Check</div>
    <div class="ce-card-body">
        <div class="row g-3">
            <div class="col-md-4"><div class="fs-sm text-muted-ce">Total Debits</div><div class="fw-600 fs-4"><?= Format::currency($totalDebits) ?></div></div>
            <div class="col-md-4"><div class="fs-sm text-muted-ce">Total Credits</div><div class="fw-600 fs-4"><?= Format::currency($totalCredits) ?></div></div>
            <div class="col-md-4"><div class="fs-sm text-muted-ce">Status</div>
                <span class="ce-badge ce-badge-<?= $isBalanced ? 'success' : 'danger' ?>"><?= $isBalanced ? 'BALANCED' : 'UNBALANCED' ?></span>
            </div>
        </div>
    </div>
</div>
<?php if (!empty($discrepancies)): ?>
<div class="ce-card mb-4 border-danger">
    <div class="ce-card-header text-danger">Balance Discrepancies Found</div>
    <div class="card-body p-0"><table class="ce-table mb-0">
        <thead><tr><th>Account</th><th class="text-end">Cached Balance</th><th class="text-end">Calculated</th><th class="text-end">Difference</th></tr></thead>
        <tbody><?php foreach ($discrepancies as $d): ?>
            <tr><td class="fw-600"><?= Escape::html($d['account_name']) ?><div class="fs-sm text-muted-ce"><?= Escape::html($d['account_code']) ?></div></td>
            <td class="text-end"><?= Format::currency((string)$d['cached_balance']) ?></td>
            <td class="text-end"><?= Format::currency((string)$d['calculated_balance']) ?></td>
            <td class="text-end fw-600 text-danger"><?= Format::currency((string)$d['difference']) ?></td></tr>
        <?php endforeach; ?>
        </tbody></table></div>
</div>
<?php else: ?>
<div class="ce-alert ce-alert-success"><i class="bi bi-check-circle me-2"></i>All account balances match their calculated ledger totals. No discrepancies found.</div>
<?php endif; ?>
