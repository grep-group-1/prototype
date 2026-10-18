<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="ce-card">
            <div class="ce-card-header">Download Account Statement</div>
            <div class="ce-card-body">
                <?php if (empty($periods)): ?>
                    <div class="ce-empty-state">
                        <i class="bi bi-file-earmark-text"></i><h5>No statements available</h5>
                        <p class="fs-sm">Statements will be available once you have transaction history.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="ce-table">
                            <thead><tr><th>Period</th><th>Format</th><th></th></tr></thead>
                            <tbody>
                                <?php foreach ($periods as $period): ?>
                                <tr>
                                    <td class="fw-600"><?= Escape::html($period['period']) ?></td>
                                    <td><span class="ce-badge ce-badge-navy">CSV</span></td>
                                    <td><a href="<?= Url::to('statements/download', ['period' => $period['period'], 'format' => 'csv']) ?>" class="btn btn-sm btn-ce-outline"><i class="bi bi-download me-1"></i>Download</a></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
