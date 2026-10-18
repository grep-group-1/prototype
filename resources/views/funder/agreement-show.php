<div class="row">
    <div class="col-lg-8">
        <div class="ce-card mb-4">
            <div class="ce-card-header">
                <span class="ce-badge ce-badge-<?= $product['product_type'] === 'profit_sharing' ? 'navy' : 'info' ?> me-2">
                    <?= $product['product_type'] === 'profit_sharing' ? 'Option A' : 'Option B' ?>
                </span>
                <?= Escape::html($product['agreement_title']) ?>
            </div>
            <div class="ce-card-body">
                <div class="ce-risk-box mb-4">
                    <h6><i class="bi bi-exclamation-triangle-fill me-1"></i> Important Risk Disclosure</h6>
                    <p>This is a <strong>simulated</strong> agreement for a university prototype. No real money is involved. 
                    Profits are NOT guaranteed. Capital may be at risk in a real scenario. 
                    This does not constitute a licensed investment service.</p>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="p-3 rounded" style="background: var(--ce-bg);">
                            <div class="fs-sm text-muted-ce">Profit Share</div>
                            <div class="fs-4 fw-bold text-navy"><?= Format::percentage($product['profit_share_pct']) ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded" style="background: var(--ce-bg);">
                            <div class="fs-sm text-muted-ce">Management Fee</div>
                            <div class="fs-4 fw-bold text-navy"><?= Format::percentage($product['management_fee_pct']) ?>/yr</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded" style="background: var(--ce-bg);">
                            <div class="fs-sm text-muted-ce">Lock-in Period</div>
                            <div class="fs-4 fw-bold text-navy"><?= (int)$product['min_lock_in_days'] ?> days</div>
                        </div>
                    </div>
                </div>

                <!-- Agreement Body (rendered as markdown-like text) -->
                <div class="border rounded p-4" style="background: var(--ce-bg); max-height: 400px; overflow-y: auto;">
                    <pre style="white-space: pre-wrap; font-family: var(--ce-font); font-size: 0.85rem; color: var(--ce-text-secondary); line-height: 1.7;"><?= Escape::html($product['agreement_body']) ?></pre>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="ce-card">
            <div class="ce-card-header">Accept Agreement</div>
            <div class="ce-card-body">
                <?php if ($existingAcceptance): ?>
                    <div class="text-center py-3">
                        <i class="bi bi-patch-check-fill text-success" style="font-size: 2.5rem;"></i>
                        <h5 class="mt-2">Agreement Accepted</h5>
                        <p class="fs-sm text-muted-ce">You accepted this arrangement on <?= Format::dateTime($existingAcceptance['accepted_at']) ?></p>
                    </div>
                <?php else: ?>
                    <form method="POST" action="<?= Url::to("agreements/{$product['id']}/accept") ?>">
                        <?= Csrf::field() ?>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="accept_risk" name="accept_risk" required>
                            <label class="form-check-label fs-sm" for="accept_risk">
                                I have read and understood the <strong>Risk Disclosure</strong>. I understand that profits are not guaranteed and capital may be at risk.
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="accept_terms" name="accept_terms" required>
                            <label class="form-check-label fs-sm" for="accept_terms">
                                I accept the terms of this funding arrangement as described above.
                            </label>
                        </div>
                        <button type="submit" class="btn btn-ce-gold w-100">Accept Agreement</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
