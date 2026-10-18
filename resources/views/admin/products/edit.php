<div class="row justify-content-center"><div class="col-lg-8">
    <div class="ce-card"><div class="ce-card-header">Edit: <?= Escape::html($product['name']) ?></div><div class="ce-card-body">
        <form method="POST" action="<?= Url::to("admin/products/{$product['id']}") ?>"><?= Csrf::field() ?>
            <div class="row g-3">
                <div class="col-md-8"><label class="ce-form-label">Name</label><input type="text" class="form-control ce-form-control" name="name" value="<?= Escape::html($product['name']) ?>" required></div>
                <div class="col-md-4"><label class="ce-form-label">Status</label><select name="status" class="form-control ce-form-control"><option value="active" <?= $product['status']==='active'?'selected':'' ?>>Active</option><option value="inactive" <?= $product['status']==='inactive'?'selected':'' ?>>Inactive</option></select></div>
                <div class="col-12"><label class="ce-form-label">Description</label><textarea name="description" class="form-control ce-form-control" rows="3"><?= Escape::html($product['description']) ?></textarea></div>
                <div class="col-md-4"><label class="ce-form-label">Profit Share %</label><input type="number" class="form-control ce-form-control" name="profit_share_pct" step="0.01" value="<?= Escape::html($product['profit_share_pct']) ?>"></div>
                <div class="col-md-4"><label class="ce-form-label">Management Fee %</label><input type="number" class="form-control ce-form-control" name="management_fee_pct" step="0.01" value="<?= Escape::html($product['management_fee_pct']) ?>"></div>
                <div class="col-md-4"><label class="ce-form-label">Entry Fee %</label><input type="number" class="form-control ce-form-control" name="entry_fee_pct" step="0.01" value="<?= Escape::html($product['entry_fee_pct']) ?>"></div>
            </div>
            <button type="submit" class="btn btn-ce-primary mt-4">Update Product</button>
        </form>
    </div></div>
</div></div>
