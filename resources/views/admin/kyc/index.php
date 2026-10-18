<div class="ce-card mb-3">
    <div class="ce-card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <select class="form-control ce-form-control" name="status">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= $statusFilter === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    <option value="requires_review" <?= $statusFilter === 'requires_review' ? 'selected' : '' ?>>Requires Review</option>
                </select>
            </div>
            <div class="col-md-4"><button type="submit" class="btn btn-ce-primary w-100">Filter</button></div>
        </form>
    </div>
</div>
<div class="ce-card"><div class="card-body p-0">
    <div class="table-responsive"><table class="ce-table mb-0">
        <thead><tr><th>User</th><th>Email</th><th>Status</th><th>Submitted</th><th></th></tr></thead>
        <tbody>
            <?php foreach ($cases as $c): ?>
            <tr>
                <td class="fw-600"><?= Escape::html($c['first_name'] . ' ' . $c['last_name']) ?></td>
                <td class="fs-sm"><?= Escape::html($c['email']) ?></td>
                <td><span class="ce-badge ce-badge-<?= $c['status'] === 'approved' ? 'success' : ($c['status'] === 'rejected' ? 'danger' : 'warning') ?>"><?= ucfirst(str_replace('_',' ',$c['status'])) ?></span></td>
                <td class="fs-sm text-muted-ce"><?= Format::dateTime($c['submitted_at'] ?? $c['created_at']) ?></td>
                <td><a href="<?= Url::to("admin/kyc/{$c['id']}") ?>" class="btn btn-sm btn-ce-outline">Review</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table></div>
</div></div>
