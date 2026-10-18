<div class="ce-card mb-3"><div class="ce-card-body"><form method="GET" class="row g-3 align-items-end">
    <div class="col-md-4"><select class="form-control ce-form-control" name="status">
        <option value="">All</option><option value="pending">Pending</option><option value="approved">Approved</option><option value="completed">Completed</option><option value="rejected">Rejected</option>
    </select></div><div class="col-md-4"><button type="submit" class="btn btn-ce-primary w-100">Filter</button></div>
</form></div></div>
<div class="ce-card"><div class="card-body p-0"><div class="table-responsive"><table class="ce-table mb-0">
    <thead><tr><th>Reference</th><th>User</th><th class="text-end">Amount</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody><?php foreach ($withdrawals as $w): ?>
        <tr>
            <td class="fw-600"><?= Escape::html($w['reference']) ?></td>
            <td><?= Escape::html($w['first_name'] . ' ' . $w['last_name']) ?></td>
            <td class="text-end fw-600"><?= Format::currency($w['amount']) ?></td>
            <td><span class="ce-badge ce-badge-<?= $w['status']==='completed'?'success':($w['status']==='rejected'?'danger':'warning') ?>"><?= ucfirst($w['status']) ?></span></td>
            <td class="fs-sm text-muted-ce"><?= Format::dateTime($w['created_at']) ?></td>
            <td><?php if (in_array($w['status'],['pending','approved']) && $auth->hasAnyRole(['admin','finance'])): ?>
                <form method="POST" action="<?= Url::to("admin/withdrawals/{$w['id']}/process") ?>" class="d-flex gap-1"><?= Csrf::field() ?>
                    <input type="hidden" name="action" value="approve"><button type="submit" class="btn btn-sm btn-ce-outline">Approve</button>
                </form>
                <form method="POST" action="<?= Url::to("admin/withdrawals/{$w['id']}/process") ?>" class="d-flex gap-1 mt-1"><?= Csrf::field() ?>
                    <input type="hidden" name="action" value="complete"><button type="submit" class="btn btn-sm btn-ce-gold">Complete</button>
                </form>
                <form method="POST" action="<?= Url::to("admin/withdrawals/{$w['id']}/process") ?>" class="d-flex gap-1 mt-1"><?= Csrf::field() ?>
                    <input type="hidden" name="action" value="reject"><input type="hidden" name="reason" value="Rejected by admin">
                    <button type="submit" class="btn btn-sm btn-ce-danger">Reject</button>
                </form>
            <?php else: ?><span class="fs-sm text-muted-ce">—</span><?php endif; ?>
            </td>
        </tr><?php endforeach; ?>
    </tbody></table></div></div></div>
