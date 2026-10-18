<div class="d-flex justify-content-between align-items-center mb-4">
    <span class="fs-sm text-muted-ce"><?= $unreadCount ?> unread notification<?= $unreadCount !== 1 ? 's' : '' ?></span>
    <?php if ($unreadCount > 0): ?>
    <form method="POST" action="<?= Url::to('notifications/read-all') ?>">
        <?= Csrf::field() ?>
        <button type="submit" class="btn btn-sm btn-ce-outline">Mark All Read</button>
    </form>
    <?php endif; ?>
</div>
<div class="ce-card">
    <div class="card-body p-0">
        <?php if (empty($notifications)): ?>
            <div class="ce-empty-state"><i class="bi bi-bell-slash"></i><h5>No notifications</h5></div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($notifications as $notif): ?>
                <div class="list-group-item border-0 px-4 py-3 <?= !$notif['is_read'] ? 'bg-light border-start border-3 border-primary' : '' ?>">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="fw-600 fs-sm"><?= Escape::html($notif['title']) ?></span>
                            <p class="mb-0 fs-sm text-muted-ce mt-1"><?= Escape::html($notif['message']) ?></p>
                        </div>
                        <div class="text-end">
                            <span class="fs-sm text-muted-ce"><?= Format::timeAgo($notif['created_at']) ?></span>
                            <?php if (!$notif['is_read']): ?>
                            <form method="POST" action="<?= Url::to("notifications/{$notif['id']}/read") ?>" class="mt-1">
                                <?= Csrf::field() ?>
                                <button type="submit" class="btn btn-sm btn-link p-0 fs-sm">Mark read</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
