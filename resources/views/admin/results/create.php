<div class="row justify-content-center"><div class="col-lg-8">
    <div class="ce-card">
        <div class="ce-card-header">Record Financial Result</div>
        <div class="ce-card-body">
            <div class="ce-risk-box mb-4">
                <h6><i class="bi bi-info-circle me-1"></i> Simulated Results</h6>
                <p>This records a simulated financial result. No real trading or profit generation occurs. All figures are test data.</p>
            </div>
            <div class="ce-stat-card mb-4"><div class="stat-label">Current Total Allocated Capital</div><div class="stat-value"><?= Format::currency($totalCapital) ?></div></div>
            <form method="POST" action="<?= Url::to('admin/results') ?>"><?= Csrf::field() ?>
                <div class="row g-3">
                    <div class="col-md-6"><label class="ce-form-label">Period Start</label><input type="date" class="form-control ce-form-control" name="period_start" required></div>
                    <div class="col-md-6"><label class="ce-form-label">Period End</label><input type="date" class="form-control ce-form-control" name="period_end" required></div>
                    <div class="col-md-6"><label class="ce-form-label">Gross Result (UGX) — negative for loss</label><input type="number" class="form-control ce-form-control" name="gross_result" required placeholder="e.g. 5000000"></div>
                    <div class="col-md-6"><label class="ce-form-label">Fees Deducted (UGX)</label><input type="number" class="form-control ce-form-control" name="fees_deducted" value="0" min="0"></div>
                    <div class="col-12"><label class="ce-form-label">Notes</label><textarea name="notes" class="form-control ce-form-control" rows="3"></textarea></div>
                </div>
                <button type="submit" class="btn btn-ce-primary mt-4">Record Result</button>
            </form>
        </div>
    </div>
</div></div>
