<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * Admin Audit Log Controller
 */
class AuditController extends BaseController
{
    public function index(): void
    {
        $page = max(1, (int)($this->get('page', 1)));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $action = $this->get('action', '');
        $where = '1=1';
        $params = [];

        if ($action) {
            $where .= ' AND al.action = ?';
            $params[] = $action;
        }

        $total = $this->db->fetchOne("SELECT COUNT(*) as cnt FROM audit_logs al WHERE {$where}", $params);
        $totalPages = max(1, ceil($total['cnt'] / $perPage));

        $logs = $this->db->fetchAll(
            "SELECT al.*, u.first_name, u.last_name, u.email
             FROM audit_logs al
             LEFT JOIN users u ON al.user_id = u.id
             WHERE {$where}
             ORDER BY al.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        // Get distinct actions for filter
        $actions = $this->db->fetchAll("SELECT DISTINCT action FROM audit_logs ORDER BY action");

        $this->render('admin/audit/index', [
            'pageTitle' => 'Audit Logs',
            'logs' => $logs,
            'page' => $page,
            'totalPages' => $totalPages,
            'actions' => $actions,
            'actionFilter' => $action,
        ]);
    }
}
