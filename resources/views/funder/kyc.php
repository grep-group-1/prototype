<div class="row">
    <div class="col-lg-8">
        <?php if ($kycCase && $kycCase['status'] === 'approved'): ?>
            <div class="ce-card mb-4">
                <div class="ce-card-body text-center py-4">
                    <i class="bi bi-patch-check-fill text-success" style="font-size: 3rem;"></i>
                    <h4 class="mt-3">Identity Verified</h4>
                    <p class="text-muted-ce">Your identity has been successfully verified (simulated).</p>
                    <div class="ce-badge ce-badge-success">Approved</div>
                    <p class="fs-sm text-muted-ce mt-2">Reference: <?= Escape::html($kycCase['verification_reference']) ?></p>
                </div>
            </div>
        <?php elseif ($kycCase && in_array($kycCase['status'], ['pending', 'requires_review'])): ?>
            <div class="ce-card mb-4">
                <div class="ce-card-body text-center py-4">
                    <i class="bi bi-hourglass-split text-warning" style="font-size: 3rem;"></i>
                    <h4 class="mt-3">Verification in Progress</h4>
                    <p class="text-muted-ce">Your verification is being reviewed. This is a simulated process.</p>
                    <span class="ce-badge ce-badge-warning"><?= ucfirst(str_replace('_', ' ', $kycCase['status'])) ?></span>
                </div>
            </div>
        <?php elseif ($kycCase && $kycCase['status'] === 'rejected'): ?>
            <div class="ce-card mb-4">
                <div class="ce-card-body text-center py-4">
                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 3rem;"></i>
                    <h4 class="mt-3">Verification Rejected</h4>
                    <p class="text-muted-ce"><?= Escape::html($kycCase['reviewer_notes'] ?? 'No reason provided.') ?></p>
                    <p class="fs-sm text-muted-ce">You may submit a new verification request below.</p>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!$kycCase || $kycCase['status'] === 'rejected' || $kycCase['status'] === 'not_started'): ?>
        <div class="ce-card">
            <div class="ce-card-header">Submit Identity Verification</div>
            <div class="ce-card-body">
                <div class="ce-risk-box">
                    <h6><i class="bi bi-info-circle me-1"></i> Simulated KYC</h6>
                    <p>This is a simulated verification process for demonstration. No real identity documents are collected. 
                    The result will be randomly assigned to demonstrate the workflow.</p>
                </div>
                <form method="POST" action="<?= Url::to('kyc/submit') ?>">
                    <?= Csrf::field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="ce-form-label">Date of Birth</label>
                            <input type="date" class="form-control ce-form-control" name="date_of_birth" 
                                   value="<?= Escape::html($profile['date_of_birth'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="ce-form-label">Nationality</label>
                            <input type="text" class="form-control ce-form-control" name="nationality" 
                                   value="<?= Escape::html($profile['nationality'] ?? '') ?>" placeholder="e.g. Ugandan">
                        </div>
                        <div class="col-md-6">
                            <label class="ce-form-label">City</label>
                            <input type="text" class="form-control ce-form-control" name="city" 
                                   value="<?= Escape::html($profile['city'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="ce-form-label">Country</label>
                            <input type="text" class="form-control ce-form-control" name="country" 
                                   value="<?= Escape::html($profile['country'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="ce-form-label">Address</label>
                            <textarea class="form-control ce-form-control" name="address" rows="2"><?= Escape::html($profile['address'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="ce-form-label">Occupation</label>
                            <input type="text" class="form-control ce-form-control" name="occupation" 
                                   value="<?= Escape::html($profile['occupation'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="ce-form-label">Source of Funds</label>
                            <input type="text" class="form-control ce-form-control" name="source_of_funds" 
                                   value="<?= Escape::html($profile['source_of_funds'] ?? '') ?>" placeholder="e.g. Business income">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-ce-primary mt-4">Submit Verification</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div class="col-lg-4">
        <div class="ce-card">
            <div class="ce-card-header">Verification Status</div>
            <div class="ce-card-body">
                <div class="mb-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="ce-badge ce-badge-<?= (!$kycCase || $kycCase['status'] === 'not_started') ? 'muted' : 'success' ?>">
                            1. Submit Info
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="ce-badge ce-badge-<?= ($kycCase && in_array($kycCase['status'], ['pending', 'requires_review'])) ? 'warning' : 'muted' ?>">
                            2. Under Review
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="ce-badge ce-badge-<?= ($kycCase && $kycCase['status'] === 'approved') ? 'success' : 'muted' ?>">
                            3. Approved
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
