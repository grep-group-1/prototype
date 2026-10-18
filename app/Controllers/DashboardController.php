<?php
namespace App\Controllers;

use App\Helpers\{Url, Flash, Format};

/**
 * Dashboard Controller (Funder)
 * 
 * Displays the main funder dashboard with account summaries,
 * balances, recent transactions, and quick actions.
 */
class DashboardController extends BaseController
{
    public function index(): void
    {
        $userId = $this->auth->userId();

        // Get user's financial account balances
        $accounts = $this->db->fetchAll(
            "SELECT * FROM financial_accounts WHERE user_id = ? AND status = 'active'",
            [$userId]
        );

        $balances = [];
        foreach ($accounts as $account) {
            $balances[$account['account_code']] = $account['balance'];
        }

        // Get active agreement
        $activeAgreement = $this->db->fetchOne(
            "SELECT aa.*, fp.name as product_name, fp.code as product_code
             FROM agreement_acceptances aa
             JOIN funding_products fp ON aa.product_id = fp.id
             WHERE aa.user_id = ? AND aa.is_active = 1
             ORDER BY aa.accepted_at DESC LIMIT 1",
            [$userId]
        );

        // Get KYC status
        $kycStatus = $this->db->fetchOne(
            "SELECT status FROM kyc_cases WHERE user_id = ? ORDER BY created_at DESC LIMIT 1",
            [$userId]
        );

        // Get recent transactions (from ledger)
        $recentTransactions = $this->db->fetchAll(
            "SELECT lj.reference, lj.description, lj.journal_type, lj.total_amount, lj.created_at
             FROM ledger_journals lj
             INNER JOIN ledger_entries le ON lj.id = le.journal_id
             INNER JOIN financial_accounts fa ON le.account_id = fa.id
             WHERE fa.user_id = ? AND lj.status = 'posted'
             ORDER BY lj.created_at DESC
             LIMIT 10",
            [$userId]
        );

        // Get pending withdrawal requests
        $pendingWithdrawals = $this->db->fetchAll(
            "SELECT * FROM withdrawal_requests 
             WHERE user_id = ? AND status IN ('pending', 'approved', 'processing')
             ORDER BY created_at DESC LIMIT 5",
            [$userId]
        );

        // Get unread notification count
        $unreadCount = $this->db->fetchOne(
            "SELECT COUNT(*) as cnt FROM notifications WHERE user_id = ? AND is_read = 0",
            [$userId]
        );

        // Get recent notifications
        $recentNotifications = $this->db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5",
            [$userId]
        );

        // Get capital allocations
        $allocations = $this->db->fetchAll(
            "SELECT * FROM capital_allocations WHERE user_id = ? AND status = 'allocated'",
            [$userId]
        );

        // Get distribution history
        $distributions = $this->db->fetchAll(
            "SELECT dc.*, frr.period_start, frr.period_end
             FROM distribution_calculations dc
             JOIN financial_result_records frr ON dc.result_record_id = frr.id
             WHERE dc.user_id = ?
             ORDER BY dc.created_at DESC LIMIT 5",
            [$userId]
        );

        $this->render('funder/dashboard', [
            'pageTitle' => 'Dashboard',
            'balances' => $balances,
            'activeAgreement' => $activeAgreement,
            'kycStatus' => $kycStatus ? $kycStatus['status'] : 'not_started',
            'recentTransactions' => $recentTransactions,
            'pendingWithdrawals' => $pendingWithdrawals,
            'unreadNotificationCount' => $unreadCount['cnt'] ?? 0,
            'recentNotifications' => $recentNotifications,
            'allocations' => $allocations,
            'distributions' => $distributions,
        ]);
    }
}
