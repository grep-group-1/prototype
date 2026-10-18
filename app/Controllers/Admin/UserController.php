<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Helpers\{Url, Flash, Csrf};

/**
 * Admin User Management Controller
 */
class UserController extends BaseController
{
    public function index(): void
    {
        $page = max(1, (int)($this->get('page', 1)));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $search = trim($this->get('search', ''));

        $where = '1=1';
        $params = [];

        if ($search) {
            $where .= ' AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $total = $this->db->fetchOne(
            "SELECT COUNT(*) as cnt FROM users u WHERE {$where}",
            $params
        );
        $totalPages = max(1, ceil($total['cnt'] / $perPage));

        $users = $this->db->fetchAll(
            "SELECT u.*, r.name as role_name, r.display_name as role_display,
                    kc.status as kyc_status
             FROM users u
             LEFT JOIN user_roles ur ON u.id = ur.user_id
             LEFT JOIN roles r ON ur.role_id = r.id
             LEFT JOIN kyc_cases kc ON kc.user_id = u.id AND kc.id = (
                 SELECT MAX(id) FROM kyc_cases WHERE user_id = u.id
             )
             WHERE {$where}
             ORDER BY u.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        $this->render('admin/users', [
            'pageTitle' => 'User Management',
            'users' => $users,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total['cnt'] ?? 0,
            'search' => $search,
        ]);
    }

    public function show(string $id): void
    {
        $user = $this->db->fetchOne(
            "SELECT u.*, r.name as role_name, r.display_name as role_display
             FROM users u
             LEFT JOIN user_roles ur ON u.id = ur.user_id
             LEFT JOIN roles r ON ur.role_id = r.id
             WHERE u.id = ?",
            [$id]
        );

        if (!$user) {
            Flash::error('User not found.');
            $this->redirect(Url::to('admin/users'));
            return;
        }

        $profile = $this->db->fetchOne("SELECT * FROM user_profiles WHERE user_id = ?", [$id]);
        $kycCases = $this->db->fetchAll("SELECT * FROM kyc_cases WHERE user_id = ? ORDER BY created_at DESC", [$id]);
        $agreements = $this->db->fetchAll(
            "SELECT aa.*, fp.name as product_name FROM agreement_acceptances aa JOIN funding_products fp ON aa.product_id = fp.id WHERE aa.user_id = ? ORDER BY aa.accepted_at DESC",
            [$id]
        );
        $accounts = $this->db->fetchAll("SELECT * FROM financial_accounts WHERE user_id = ?", [$id]);

        $this->render('admin/user-show', [
            'pageTitle' => 'User Details',
            'user' => $user,
            'profile' => $profile,
            'kycCases' => $kycCases,
            'agreements' => $agreements,
            'accounts' => $accounts,
        ]);
    }

    public function updateStatus(string $id): void
    {
        if (!Csrf::validate($this->post('csrf_token'))) {
            Flash::error('Invalid request.');
            $this->redirect(Url::to('admin/users'));
            return;
        }

        $newStatus = $this->post('status');
        $allowedStatuses = ['active', 'suspended', 'closed'];
        
        if (!in_array($newStatus, $allowedStatuses)) {
            Flash::error('Invalid status.');
            $this->redirect(Url::to("admin/users/{$id}"));
            return;
        }

        $user = $this->db->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
        if (!$user) {
            Flash::error('User not found.');
            $this->redirect(Url::to('admin/users'));
            return;
        }

        $oldStatus = $user['status'];
        $this->db->query("UPDATE users SET status = ? WHERE id = ?", [$newStatus, $id]);
        
        $this->audit('user_status_changed', 'user', (int)$id, 
            ['status' => $oldStatus], ['status' => $newStatus],
            "Changed user status from {$oldStatus} to {$newStatus}");

        Flash::success("User status updated to {$newStatus}.");
        $this->redirect(Url::to("admin/users/{$id}"));
    }
}
