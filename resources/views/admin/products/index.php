<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Funding Products</h4>
    <a href="<?= Url::to('admin/products/create') ?>" class="btn btn-ce-primary"><i class="bi bi-plus me-1"></i>Create Product</a>
</div>
<div class="ce-card"><div class="card-body p-0"><div class="table-responsive"><table class="ce-table mb-0">
    <thead><tr><th>Code</th><th>Name</th><th>Type</th><th>Share %</th><th>Status</th><th>Acceptances</th><th></th></tr></thead>
    <tbody><?php foreach ($products as $p): ?>
        <tr><td class="fw-600"><?= Escape::html($p['code']) ?></td><td><?= Escape::html($p['name']) ?></td>
        <td><span class="ce-badge ce-badge-navy"><?= ucfirst(str_replace('_',' ',$p['product_type'])) ?></span></td>
        <td><?= Format::percentage($p['profit_share_pct']) ?></td>
        <td><span class="ce-badge ce-badge-<?= $p['status']==='active'?'success':'muted' ?>"><?= ucfirst($p['status']) ?></span></td>
        <td><?= (int)$p['active_acceptances'] ?></td>
        <td><a href="<?= Url::to("admin/products/{$p['id']}/edit") ?>" class="fs-sm">Edit</a></td></tr>
    <?php endforeach; ?>
</tbody></table></div></div></div>
