<div class="row">
    <div class="col-lg-8">
        <div class="ce-card mb-4">
            <div class="ce-card-header">KYC Case #<?= $case['id'] ?></div>
            <div class="ce-card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-4"><div class="fs-sm text-muted-ce">Applicant</div><div class="fw-600"><?= Escape::html($case['first_name'] . ' ' . $case['last_name']) ?></div></div>
                    <div class="col-md-4"><div class="fs-sm text-muted-ce">Email</div><div><?= Escape::html($case['email']) ?></div></div>
                    <div class="col-md-4"><div class="fs-sm text-muted-ce">Status</div><span class="ce-badge ce-badge-<?= $case['status'] === 'approved' ? 'success' : 'warning' ?>"><?= ucfirst(str_replace('_',' ',$case['status'])) ?></span></div>
                    <div class="col-md-4"><div class="fs-sm text-muted-ce">Reference</div><div><?= Escape::html($case['verification_reference'] ?? '—') ?></div></div>
                    <div class="col-md-4"><div class="fs-sm text-muted-ce">Simulation Result</div><div><?= ucfirst(Escape::html($case['simulation_result'] ?? '—')) ?></div></div>
                </div>
                <?php if ($profile): ?>
                <h6 class="fw-600 mb-3">Submitted Information</h6>
                <div class="row g-3">
                    <div class="col-md-6"><div class="fs-sm text-muted-ce">Date of Birth</div><div><?= Escape::html($profile['date_of_birth'] ?? '—') ?></div></div>
                    <div class="col-md-6"><div class="fs-sm text-muted-ce">Nationality</div><div><?= Escape::html($profile['nationality'] ?? '—') ?></div></div>
                    <div class="col-md-6"><div class="fs-sm text-muted-ce">Occupation</div><div><?= Escape::html($profile['occupation'] ?? '—') ?></div></div>
                    <div class="col-md-6"><div class="fs-sm text-muted-ce">Source of Funds</div><div><?= Escape::html($profile['source_of_funds'] ?? '—') ?></div></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="ce-card">
            <div class="ce-card-header">Review Decision</div>
            <div class="ce-card-body">
                <form method="POST" action="<?= Url::to("admin/kyc/{$case['id']}/review") ?>">
                    <?= Csrf::field() ?>
                    <div class="mb-3">
                        <label class="ce-form-label">Decision</label>
                        <select name="status" class="form-control ce-form-control" required>
                            <option value="approved">Approve</option>
                            <option value="rejected">Reject</option>
                            <option value="requires_review">Requires Additional Review</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="ce-form-label">Notes</label>
                        <textarea name="notes" class="form-control ce-form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-ce-primary">Submit Review</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4"><a href="<?= Url::to('admin/kyc') ?>" class="btn btn-ce-outline w-100"><i class="bi bi-arrow-left me-2"></i>Back</a></div>
</div>
