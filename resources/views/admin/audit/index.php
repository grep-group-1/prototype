<div class="ce-card mb-3"><div class="ce-card-body"><form method="GET" class="row g-3 align-items-end">
    <div class="col-md-4"><select class="form-control ce-form-control" name="action">
        <option value="">All Actions</option>
        <?php foreach ($actions as $a): ?><option value="<?= Escape::html($a['action']) ?>" <?= $actionFilter === $a['action'] ? 'selected' : '' ?>><?= Escape::html($a['action']) ?></option><?php endforeach; ?>
    </select></div><div class="col-md-4"><button type="submit" class="btn btn-ce-primary w-100">Filter</button></div>
</form></div></div>
<div class="ce-card"><div class="card-body p-0"><div class="table-responsive"><table class="ce-table mb-0">
    <thead><tr><th>Time</th><th>User</th><th>Action</th><th>Entity</th><th>Description</th></tr></thead>
    <tbody><?php foreach ($logs as $l): ?>
        <tr><td class="fs-sm text-muted-ce"><?= Format::dateTime($l['created_at']) ?></td>
        <td class="fs-sm"><?= Escape::html(($l['first_name'] ?? '') . ' ' . ($l['last_name'] ?? 'System')) ?></td>
        <td><span class="ce-badge ce-badge-navy"><?= Escape::html($l['action']) ?></span></td>
        <td class="fs-sm text-muted-ce"><?= Escape::html($l['entity_type'] ?? '—') ?><?= $l['entity_id'] ? ' #' . $l['entity_id'] : '' ?></td>
        <td class="fs-sm"><?= Escape::html(Format::truncate($l['description'] ?? '', 60)) ?></td></tr>
    <?php endforeach; ?>
    </tbody></table></div></div></div>
<?php if ($totalPages > 1): ?>
<nav class="mt-3"><ul class="pagination ce-pagination justify-content-center">
    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
    <li class="page-item <?= $p===$page?'active':'' ?>"><a class="page-link" href="?page=<?= $p ?>&action=<?= Escape::url($actionFilter) ?>"><?= $p ?></a></li>
    <?php endfor; ?>
</ul></nav>
<?php endif; ?>
