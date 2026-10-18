<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="ce-stat-card"><div class="stat-label">Total Deposits</div><div class="stat-value"><?= Format::currency($totalDeposits) ?></div></div></div>
    <div class="col-md-3"><div class="ce-stat-card"><div class="stat-label">Total Allocated</div><div class="stat-value"><?= Format::currency($totalAllocated) ?></div></div></div>
    <div class="col-md-3"><div class="ce-stat-card"><div class="stat-label">Total Distributed</div><div class="stat-value"><?= Format::currency($totalDistributions) ?></div></div></div>
    <div class="col-md-3"><div class="ce-stat-card"><div class="stat-label">Total Withdrawn</div><div class="stat-value"><?= Format::currency($totalWithdrawn) ?></div></div></div>
</div>
<div class="ce-card">
    <div class="ce-card-header">System Account Balances</div>
    <div class="card-body p-0"><div class="table-responsive"><table class="ce-table mb-0">
        <thead><tr><th>Code</th><th>Name</th><th>Type</th><th>Currency</th><th class="text-end">Balance</th></tr></thead>
        <tbody><?php foreach ($accounts as $a): ?>
            <tr><td class="fw-600"><?= Escape::html($a['account_code']) ?></td><td><?= Escape::html($a['account_name']) ?></td>
            <td><span class="ce-badge ce-badge-muted"><?= ucfirst($a['account_type']) ?></span></td>
            <td><?= Escape::html($a['currency']) ?></td><td class="text-end fw-600"><?= Format::currency($a['balance'], $a['currency']) ?></td></tr>
        <?php endforeach; ?>
    </tbody></table></div></div>
</div>
