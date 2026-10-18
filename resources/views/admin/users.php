<div class="ce-card mb-4">
    <div class="ce-card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-8">
                <input type="text" class="form-control ce-form-control" name="search" placeholder="Search by name or email..." value="<?= Escape::html($search) ?>">
            </div>
            <div class="col-md-4"><button type="submit" class="btn btn-ce-primary w-100">Search</button></div>
        </form>
    </div>
</div>
<div class="ce-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="ce-table mb-0">
                <thead><tr><th>User</th><th>Email</th><th>Role</th><th>KYC</th><th>Status</th><th>Joined</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="fw-600"><?= Escape::html($u['first_name'] . ' ' . $u['last_name']) ?></td>
                        <td class="fs-sm"><?= Escape::html($u['email']) ?></td>
                        <td><span class="ce-badge ce-badge-navy"><?= Escape::html($u['role_display'] ?? $u['role_name'] ?? '—') ?></span></td>
                        <td><span class="ce-badge ce-badge-<?= $u['kyc_status'] === 'approved' ? 'success' : ($u['kyc_status'] === 'pending' ? 'warning' : 'muted') ?>"><?= ucfirst(str_replace('_',' ',$u['kyc_status'] ?? 'none')) ?></span></td>
                        <td><span class="ce-badge ce-badge-<?= $u['status'] === 'active' ? 'success' : 'danger' ?>"><?= ucfirst($u['status']) ?></span></td>
                        <td class="fs-sm text-muted-ce"><?= Format::date($u['created_at']) ?></td>
                        <td><a href="<?= Url::to("admin/users/{$u['id']}") ?>" class="fs-sm">View</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php if ($totalPages > 1): ?>
<nav class="mt-3"><ul class="pagination ce-pagination justify-content-center">
    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
    <li class="page-item <?= $p === $page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $p ?>&search=<?= Escape::url($search) ?>"><?= $p ?></a></li>
    <?php endfor; ?>
</ul></nav>
<?php endif; ?>
