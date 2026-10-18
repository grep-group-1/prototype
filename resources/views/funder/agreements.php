<div class="row g-4">
    <?php if (empty($products)): ?>
        <div class="col-12">
            <div class="ce-empty-state">
                <i class="bi bi-file-earmark-text"></i>
                <h5>No funding arrangements available</h5>
                <p class="fs-sm">Please check back later.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
        <div class="col-lg-6">
            <div class="ce-agreement-card <?= in_array($product['id'], $acceptedProductIds) ? 'selected' : '' ?>">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="ce-badge ce-badge-<?= $product['product_type'] === 'profit_sharing' ? 'navy' : 'info' ?>">
                        <?= $product['product_type'] === 'profit_sharing' ? 'Option A' : 'Option B' ?>
                    </span>
                    <?php if (in_array($product['id'], $acceptedProductIds)): ?>
                        <span class="ce-badge ce-badge-success"><i class="bi bi-check-circle me-1"></i>Accepted</span>
                    <?php endif; ?>
                </div>
                <h5 class="mb-2"><?= Escape::html($product['name']) ?></h5>
                <p class="fs-sm text-muted-ce mb-3"><?= Escape::html(Format::truncate($product['description'], 150)) ?></p>
                
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="fs-sm text-muted-ce">Profit Share</div>
                        <div class="fw-600"><?= Format::percentage($product['profit_share_pct']) ?></div>
                    </div>
                    <div class="col-6">
                        <div class="fs-sm text-muted-ce">Management Fee</div>
                        <div class="fw-600"><?= Format::percentage($product['management_fee_pct']) ?>/yr</div>
                    </div>
                    <div class="col-6">
                        <div class="fs-sm text-muted-ce">Lock-in Period</div>
                        <div class="fw-600"><?= $product['min_lock_in_days'] ?> days</div>
                    </div>
                    <div class="col-6">
                        <div class="fs-sm text-muted-ce">Entry Fee</div>
                        <div class="fw-600"><?= Format::percentage($product['entry_fee_pct']) ?></div>
                    </div>
                </div>

                <a href="<?= Url::to("agreements/{$product['id']}") ?>" class="btn-ce-outline">
                    View Full Agreement <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
