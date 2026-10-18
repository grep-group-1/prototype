<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Financial Results</h4>
    <?php if ($auth->hasAnyRole(['admin','finance'])): ?>
    <a href="<?= Url::to('admin/results/create') ?>" class="btn btn-ce-primary"><i class="bi bi-plus me-1"></i>Record Result</a>
    <?php endif; ?>
</div>
<div class="ce-card"><div class="card-body p-0"><div class="table-responsive"><table class="ce-table mb-0">
    <thead><tr><th>Period</th><th class="text-end">Total Capital</th><th class="text-end">Gross Result</th><th class="text-end">Net Result</th><th>Status</th><th>Action</th></tr></thead>
    <tbody><?php foreach ($results as $r): ?>
        <tr>
            <td><?= Escape::html($r['period_start']) ?> to <?= Escape::html($r['period_end']) ?></td>
            <td class="text-end"><?= Format::currency($r['total_capital']) ?></td>
            <td class="text-end <?= (int)$r['gross_result'] >= 0 ? 'text-success' : 'text-danger' ?>"><?= Format::currency($r['gross_result']) ?></td>
            <td class="text-end fw-600 <?= (int)$r['net_result'] >= 0 ? 'text-success' : 'text-danger' ?>"><?= Format::currency($r['net_result']) ?></td>
            <td><span class="ce-badge ce-badge-<?= $r['status']==='approved'?'success':($r['status']==='rejected'?'danger':'warning') ?>"><?= ucfirst(str_replace('_',' ',$r['status'])) ?></span></td>
            <td><?php if (in_array($r['status'],['draft','pending_review']) && $auth->isAdmin()): ?>
                <form method="POST" action="<?= Url::to("admin/results/{$r['id']}/approve") ?>" class="d-inline"><?= Csrf::field() ?>
                    <input type="hidden" name="action" value="approve"><button type="submit" class="btn btn-sm btn-ce-gold">Approve</button>
                </form><?php else: ?><span class="fs-sm text-muted-ce">—</span><?php endif; ?>
            </td>
        </tr><?php endforeach; ?>
    </tbody></table></div></div></div>
