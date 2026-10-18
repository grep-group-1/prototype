<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="ce-card">
            <div class="ce-card-header">Request Withdrawal</div>
            <div class="ce-card-body">
                <div class="ce-stat-card mb-4">
                    <div class="stat-label">Withdrawable Balance</div>
                    <div class="stat-value"><?= Format::currency($withdrawableBalance) ?></div>
                </div>
                <form method="POST" action="<?= Url::to('withdrawals') ?>">
                    <?= Csrf::field() ?>
                    <div class="mb-3">
                        <label for="amount" class="ce-form-label">Withdrawal Amount (UGX)</label>
                        <input type="number" class="form-control ce-form-control" id="amount" name="amount" min="10000" max="<?= (int)$withdrawableBalance ?>" required>
                        <div class="form-text">Maximum: <?= Format::currency($withdrawableBalance) ?></div>
                    </div>
                    <button type="submit" class="btn btn-ce-primary w-100">Submit Withdrawal Request</button>
                </form>
            </div>
        </div>
    </div>
</div>
