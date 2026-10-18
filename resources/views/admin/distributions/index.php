<div class="ce-card"><div class="card-body p-0"><div class="table-responsive"><table class="ce-table mb-0">
    <thead><tr><th>Funder</th><th>Period</th><th class="text-end">Capital</th><th class="text-end">Share %</th><th class="text-end">Distribution</th><th>Status</th><th>Action</th></tr></thead>
    <tbody><?php foreach ($distributions as $d): ?>
        <tr>
            <td class="fw-600"><?= Escape::html($d['first_name'] . ' ' . $d['last_name']) ?></td>
            <td class="fs-sm"><?= Escape::html($d['period_start']) ?> to <?= Escape::html($d['period_end']) ?></td>
            <td class="text-end"><?= Format::currency($d['capital_amount']) ?></td>
            <td class="text-end"><?= Format::percentage($d['profit_share_pct']) ?></td>
            <td class="text-end fw-600"><?= Format::currency($d['net_distribution']) ?></td>
            <td><span class="ce-badge ce-badge-<?= $d['status']==='paid'?'success':($d['status']==='approved'?'info':'warning') ?>"><?= ucfirst($d['status']) ?></span></td>
            <td><?php if ($d['status'] === 'calculated' && $auth->hasAnyRole(['admin','finance'])): ?>
                <form method="POST" action="<?= Url::to("admin/distributions/{$d['id']}/approve") ?>" class="d-inline"><?= Csrf::field() ?>
                    <input type="hidden" name="action" value="approve"><button type="submit" class="btn btn-sm btn-ce-gold">Approve & Pay</button>
                </form><?php else: ?><span class="fs-sm text-muted-ce">—</span><?php endif; ?>
            </td>
        </tr><?php endforeach; ?>
    </tbody></table></div></div></div>
