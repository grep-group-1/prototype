<div class="row">
    <div class="col-lg-8">
        <div class="ce-card">
            <div class="ce-card-header">Withdrawal Details</div>
            <div class="ce-card-body">
                <div class="row g-3">
                    <div class="col-md-6"><div class="fs-sm text-muted-ce">Reference</div><div class="fw-600"><?= Escape::html($withdrawal['reference']) ?></div></div>
                    <div class="col-md-6"><div class="fs-sm text-muted-ce">Amount</div><div class="fw-600 fs-4 text-navy"><?= Format::currency($withdrawal['amount']) ?></div></div>
                    <div class="col-md-6"><div class="fs-sm text-muted-ce">Status</div><span class="ce-badge ce-badge-<?= $withdrawal['status'] === 'completed' ? 'success' : 'warning' ?>"><?= ucfirst($withdrawal['status']) ?></span></div>
                    <div class="col-md-6"><div class="fs-sm text-muted-ce">Date</div><div><?= Format::dateTime($withdrawal['created_at']) ?></div></div>
                    <?php if ($withdrawal['payment_reference']): ?>
                    <div class="col-md-6"><div class="fs-sm text-muted-ce">Payment Reference</div><div><?= Escape::html($withdrawal['payment_reference']) ?></div></div>
                    <?php endif; ?>
                    <?php if ($withdrawal['rejection_reason']): ?>
                    <div class="col-12"><div class="ce-alert ce-alert-danger">Rejection reason: <?= Escape::html($withdrawal['rejection_reason']) ?></div></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4"><a href="<?= Url::to('withdrawals') ?>" class="btn btn-ce-outline w-100"><i class="bi bi-arrow-left me-2"></i>Back</a></div>
</div>
