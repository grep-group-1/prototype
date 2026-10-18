<div class="ce-card mb-3"><div class="ce-card-body"><form method="GET" class="row g-3 align-items-end">
    <div class="col-md-4"><select class="form-control ce-form-control" name="status">
        <option value="">All</option><option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>Completed</option><option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
    </select></div><div class="col-md-4"><button type="submit" class="btn btn-ce-primary w-100">Filter</button></div>
</form></div></div>
<div class="ce-card"><div class="card-body p-0"><div class="table-responsive"><table class="ce-table mb-0">
    <thead><tr><th>Reference</th><th>User</th><th class="text-end">Amount</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
    <tbody><?php foreach ($deposits as $d): ?>
        <tr>
            <td class="fw-600"><?= Escape::html($d['reference']) ?></td>
            <td><?= Escape::html($d['first_name'] . ' ' . $d['last_name']) ?></td>
            <td class="text-end fw-600"><?= Format::currency($d['amount']) ?></td>
            <td><span class="ce-badge ce-badge-<?= $d['status']==='completed'?'success':'warning' ?>"><?= ucfirst($d['status']) ?></span></td>
            <td class="fs-sm text-muted-ce"><?= Format::dateTime($d['created_at']) ?></td>
            <td><?php if ($d['status'] === 'completed'): ?>
                <form method="POST" action="<?= Url::to("admin/deposits/{$d['id']}/confirm") ?>" class="d-inline"><?= Csrf::field() ?>
                    <input type="hidden" name="action" value="allocate"><button type="submit" class="btn btn-sm btn-ce-gold">Allocate</button>
                </form><?php else: ?><span class="fs-sm text-muted-ce">—</span><?php endif; ?>
            </td>
        </tr><?php endforeach; ?>
    </tbody></table></div></div></div>
