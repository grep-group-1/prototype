<div class="row">
    <div class="col-lg-8">
        <div class="ce-card mb-4">
            <div class="ce-card-header">User Details</div>
            <div class="ce-card-body">
                <div class="row g-3">
                    <div class="col-md-6"><div class="fs-sm text-muted-ce">Name</div><div class="fw-600"><?= Escape::html($user['first_name'] . ' ' . $user['last_name']) ?></div></div>
                    <div class="col-md-6"><div class="fs-sm text-muted-ce">Email</div><div><?= Escape::html($user['email']) ?></div></div>
                    <div class="col-md-4"><div class="fs-sm text-muted-ce">Role</div><span class="ce-badge ce-badge-navy"><?= Escape::html($user['role_display'] ?? '—') ?></span></div>
                    <div class="col-md-4"><div class="fs-sm text-muted-ce">Status</div><span class="ce-badge ce-badge-<?= $user['status'] === 'active' ? 'success' : 'danger' ?>"><?= ucfirst($user['status']) ?></span></div>
                    <div class="col-md-4"><div class="fs-sm text-muted-ce">Joined</div><div><?= Format::date($user['created_at']) ?></div></div>
                </div>
            </div>
        </div>
        <?php if ($auth->isAdmin()): ?>
        <div class="ce-card mb-4">
            <div class="ce-card-header">Update Status</div>
            <div class="ce-card-body">
                <form method="POST" action="<?= Url::to("admin/users/{$user['id']}/status") ?>" class="d-flex gap-2">
                    <?= Csrf::field() ?>
                    <select name="status" class="form-control ce-form-control">
                        <option value="active">Active</option><option value="suspended">Suspended</option><option value="closed">Closed</option>
                    </select>
                    <button type="submit" class="btn btn-ce-primary">Update</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
        <?php if (!empty($accounts)): ?>
        <div class="ce-card">
            <div class="ce-card-header">Financial Accounts</div>
            <div class="card-body p-0">
                <table class="ce-table mb-0">
                    <thead><tr><th>Account</th><th>Type</th><th class="text-end">Balance</th></tr></thead>
                    <tbody>
                        <?php foreach ($accounts as $acc): ?>
                        <tr><td><?= Escape::html($acc['account_name']) ?><div class="fs-sm text-muted-ce"><?= Escape::html($acc['account_code']) ?></div></td>
                        <td><span class="ce-badge ce-badge-muted"><?= ucfirst($acc['account_type']) ?></span></td>
                        <td class="text-end fw-600"><?= Format::currency($acc['balance']) ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div class="col-lg-4">
        <a href="<?= Url::to('admin/users') ?>" class="btn btn-ce-outline w-100 mb-3"><i class="bi bi-arrow-left me-2"></i>Back to Users</a>
    </div>
</div>
