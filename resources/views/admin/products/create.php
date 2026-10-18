<div class="row justify-content-center"><div class="col-lg-8">
    <div class="ce-card"><div class="ce-card-header">Create Funding Product</div><div class="ce-card-body">
        <form method="POST" action="<?= Url::to('admin/products') ?>"><?= Csrf::field() ?>
            <div class="row g-3">
                <div class="col-md-4"><label class="ce-form-label">Code</label><input type="text" class="form-control ce-form-control" name="code" required placeholder="e.g. PROFIT_SHARE_C"></div>
                <div class="col-md-8"><label class="ce-form-label">Name</label><input type="text" class="form-control ce-form-control" name="name" required></div>
                <div class="col-12"><label class="ce-form-label">Description</label><textarea name="description" class="form-control ce-form-control" rows="3"></textarea></div>
                <div class="col-md-6"><label class="ce-form-label">Type</label><select name="product_type" class="form-control ce-form-control"><option value="profit_sharing">Profit Sharing</option><option value="periodic_distribution">Periodic Distribution</option></select></div>
                <div class="col-md-6"><label class="ce-form-label">Profit Share %</label><input type="number" class="form-control ce-form-control" name="profit_share_pct" step="0.01" required></div>
                <div class="col-md-4"><label class="ce-form-label">Management Fee %</label><input type="number" class="form-control ce-form-control" name="management_fee_pct" step="0.01" value="0"></div>
                <div class="col-md-4"><label class="ce-form-label">Entry Fee %</label><input type="number" class="form-control ce-form-control" name="entry_fee_pct" step="0.01" value="0"></div>
                <div class="col-md-4"><label class="ce-form-label">Min Lock-in (days)</label><input type="number" class="form-control ce-form-control" name="min_lock_in_days" value="0"></div>
            </div>
            <button type="submit" class="btn btn-ce-primary mt-4">Create Product</button>
        </form>
    </div></div>
</div></div>
