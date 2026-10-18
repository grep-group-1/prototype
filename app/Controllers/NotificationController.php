<?php
namespace App\Controllers;

use App\Helpers\{Url, Flash, Csrf};

/**
 * Notification Controller
 * 
 * Manages in-app notifications for authenticated users.
 */
class NotificationController extends BaseController
{
    public function index(): void
    {
        $userId = $this->auth->userId();
        $page = max(1, (int)($this->get('page', 1)));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $total = $this->db->fetchOne(
            "SELECT COUNT(*) as cnt FROM notifications WHERE user_id = ?",
            [$userId]
        );
        $totalPages = max(1, ceil($total['cnt'] / $perPage));

        $notifications = $this->db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$userId, $perPage, $offset]
        );

        $unreadCount = $this->db->fetchOne(
            "SELECT COUNT(*) as cnt FROM notifications WHERE user_id = ? AND is_read = 0",
            [$userId]
        );

        $this->render('funder/notifications', [
            'pageTitle' => 'Notifications',
            'notifications' => $notifications,
            'page' => $page,
            'totalPages' => $totalPages,
            'unreadCount' => $unreadCount['cnt'] ?? 0,
        ]);
    }

    public function markRead(string $id): void
    {
        $userId = $this->auth->userId();
        $this->db->query(
            "UPDATE notifications SET is_read = 1, read_at = NOW() WHERE id = ? AND user_id = ?",
            [$id, $userId]
        );
        $this->redirectBack();
    }

    public function markAllRead(): void
    {
        $userId = $this->auth->userId();
        $this->db->query(
            "UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = ? AND is_read = 0",
            [$userId]
        );
        Flash::success('All notifications marked as read.');
        $this->redirect(Url::to('notifications'));
    }
}
