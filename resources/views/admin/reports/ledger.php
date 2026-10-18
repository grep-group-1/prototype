<div class="ce-card"><div class="ce-card-header">Ledger Journal</div><div class="card-body p-0"><div class="table-responsive"><table class="ce-table mb-0">
    <thead><tr><th>Reference</th><th>Type</th><th>Description</th><th class="text-end">Amount</th><th>Created By</th><th>Date</th></tr></thead>
    <tbody><?php foreach ($journals as $j): ?>
        <tr><td class="fw-600"><?= Escape::html($j['reference']) ?></td>
        <td><span class="ce-badge ce-badge-navy"><?= ucfirst(str_replace('_',' ',$j['journal_type'])) ?></span></td>
        <td class="fs-sm"><?= Escape::html(Format::truncate($j['description'], 50)) ?></td>
        <td class="text-end fw-600"><?= Format::currency($j['total_amount']) ?></td>
        <td class="fs-sm"><?= Escape::html(($j['first_name'] ?? '') . ' ' . ($j['last_name'] ?? 'System')) ?></td>
        <td class="fs-sm text-muted-ce"><?= Format::dateTime($j['created_at']) ?></td></tr>
    <?php endforeach; ?>
    </tbody></table></div></div></div>
<?php if ($totalPages > 1): ?>
<nav class="mt-3"><ul class="pagination ce-pagination justify-content-center">
    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
    <li class="page-item <?= $p===$page?'active':'' ?>"><a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a></li>
    <?php endfor; ?>
</ul></nav>
<?php endif; ?>
