<div class="row justify-content-center">
    <div class="col-lg-6">
        <?php if (!$hasAgreement): ?>
            <div class="ce-card">
                <div class="ce-card-body text-center py-4">
                    <i class="bi bi-file-earmark-x text-warning" style="font-size: 2.5rem;"></i>
                    <h5 class="mt-3">Agreement Required</h5>
                    <p class="text-muted-ce">You must accept a funding arrangement before making a deposit.</p>
                    <a href="<?= Url::to('agreements') ?>" class="btn btn-ce-primary">View Agreements</a>
                </div>
            </div>
        <?php else: ?>
            <div class="ce-card">
                <div class="ce-card-header">New Simulated Deposit</div>
                <div class="ce-card-body">
                    <div class="ce-risk-box mb-4">
                        <h6><i class="bi bi-info-circle me-1"></i> Test Mode</h6>
                        <p>This is a simulated deposit. No real money will be transferred. 
                        The test gateway will simulate a successful deposit for demonstration purposes.</p>
                    </div>
                    <form method="POST" action="<?= Url::to('deposits') ?>">
                        <?= Csrf::field() ?>
                        <div class="mb-3">
                            <label for="amount" class="ce-form-label">Deposit Amount (UGX)</label>
                            <input type="number" class="form-control ce-form-control" id="amount" name="amount" 
                                   min="100000" max="1000000000" step="1000" required placeholder="e.g. 5000000">
                            <div class="form-text">Minimum: UGX 100,000. Maximum: UGX 1,000,000,000.</div>
                        </div>
                        <div class="mb-4">
                            <label for="payment_method" class="ce-form-label">Payment Method</label>
                            <select class="form-control ce-form-control" id="payment_method" name="payment_method">
                                <option value="test_gateway">Test Gateway (Simulated)</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-ce-primary w-100">
                            <i class="bi bi-plus-circle me-2"></i>Initiate Deposit
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
