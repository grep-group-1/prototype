<div class="row"><div class="col-lg-8">
    <div class="ce-card mb-4">
        <div class="ce-card-header">Active Funding Products</div>
        <div class="card-body p-0"><table class="ce-table mb-0">
            <thead><tr><th>Code</th><th>Name</th><th>Type</th><th>Share %</th><th>Status</th><th></th></tr></thead>
            <tbody><?php foreach ($products as $p): ?>
                <tr><td class="fw-600"><?= Escape::html($p['code']) ?></td><td><?= Escape::html($p['name']) ?></td>
                <td><span class="ce-badge ce-badge-navy"><?= ucfirst(str_replace('_',' ',$p['product_type'])) ?></span></td>
                <td><?= Format::percentage($p['profit_share_pct']) ?></td>
                <td><span class="ce-badge ce-badge-<?= $p['status']==='active'?'success':'muted' ?>"><?= ucfirst($p['status']) ?></span></td>
                <td><a href="<?= Url::to("admin/products/{$p['id']}/edit") ?>" class="fs-sm">Edit</a></td></tr>
            <?php endforeach; ?>
        </tbody></table></div>
    </div>
    <div class="ce-card">
        <div class="ce-card-header">System Information</div>
        <div class="ce-card-body">
            <div class="row g-3">
                <div class="col-md-6"><div class="fs-sm text-muted-ce">PHP Version</div><div><?= phpversion() ?></div></div>
                <div class="col-md-6"><div class="fs-sm text-muted-ce">Environment</div><div>Local (XAMPP)</div></div>
                <div class="col-md-6"><div class="fs-sm text-muted-ce">Test Mode</div><span class="ce-badge ce-badge-warning">Enabled</span></div>
                <div class="col-md-6"><div class="fs-sm text-muted-ce">Timezone</div><div><?= date_default_timezone_get() ?></div></div>
            </div>
        </div>
    </div>
</div></div>
