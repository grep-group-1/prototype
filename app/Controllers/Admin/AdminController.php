<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * Admin Dashboard Controller
 */
class AdminController extends BaseController
{
    public function index(): void
    {
        // System overview statistics
        $totalUsers = $this->db->fetchOne("SELECT COUNT(*) as cnt FROM users WHERE status = 'active'");
        $pendingKyc = $this->db->fetchOne("SELECT COUNT(*) as cnt FROM kyc_cases WHERE status = 'pending'");
        $pendingDeposits = $this->db->fetchOne("SELECT COUNT(*) as cnt FROM payment_transactions WHERE status = 'pending'");
        $pendingWithdrawals = $this->db->fetchOne("SELECT COUNT(*) as cnt FROM withdrawal_requests WHERE status IN ('pending', 'approved')");
        $pendingResults = $this->db->fetchOne("SELECT COUNT(*) as cnt FROM financial_result_records WHERE status = 'pending_review'");

        // Total deposits processed
        $totalDeposits = $this->db->fetchOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM payment_transactions WHERE status = 'completed'"
        );

        // Active allocations
        $totalAllocated = $this->db->fetchOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM capital_allocations WHERE status = 'allocated'"
        );

        // Recent activity
        $recentActivity = $this->db->fetchAll(
            "SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 10"
        );

        $this->render('admin/dashboard', [
            'pageTitle' => 'Admin Dashboard',
            'totalUsers' => $totalUsers['cnt'] ?? 0,
            'pendingKyc' => $pendingKyc['cnt'] ?? 0,
            'pendingDeposits' => $pendingDeposits['cnt'] ?? 0,
            'pendingWithdrawals' => $pendingWithdrawals['cnt'] ?? 0,
            'pendingResults' => $pendingResults['cnt'] ?? 0,
            'totalDeposits' => $totalDeposits['total'] ?? '0',
            'totalAllocated' => $totalAllocated['total'] ?? '0',
            'recentActivity' => $recentActivity,
        ]);
    }
}
