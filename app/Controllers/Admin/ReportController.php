<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Helpers\{Url, Flash, Format};

/**
 * Admin Report Controller
 */
class ReportController extends BaseController
{
    public function index(): void
    {
        // Summary statistics
        $totalDeposits = $this->db->fetchOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM payment_transactions WHERE status = 'completed'"
        );
        $totalAllocated = $this->db->fetchOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM capital_allocations WHERE status = 'allocated'"
        );
        $totalWithdrawn = $this->db->fetchOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM withdrawal_requests WHERE status = 'completed'"
        );
        $totalDistributions = $this->db->fetchOne(
            "SELECT COALESCE(SUM(net_distribution), 0) as total FROM distribution_calculations WHERE status = 'paid'"
        );

        // Account balances
        $accounts = $this->db->fetchAll(
            "SELECT * FROM financial_accounts ORDER BY account_type, account_code"
        );

        $this->render('admin/reports/index', [
            'pageTitle' => 'Financial Reports',
            'totalDeposits' => $totalDeposits['total'] ?? '0',
            'totalAllocated' => $totalAllocated['total'] ?? '0',
            'totalWithdrawn' => $totalWithdrawn['total'] ?? '0',
            'totalDistributions' => $totalDistributions['total'] ?? '0',
            'accounts' => $accounts,
        ]);
    }

    public function ledger(): void
    {
        $page = max(1, (int)($this->get('page', 1)));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $total = $this->db->fetchOne("SELECT COUNT(*) as cnt FROM ledger_journals WHERE status = 'posted'");
        $totalPages = max(1, ceil($total['cnt'] / $perPage));

        $journals = $this->db->fetchAll(
            "SELECT lj.*, u.first_name, u.last_name
             FROM ledger_journals lj
             LEFT JOIN users u ON lj.created_by = u.id
             WHERE lj.status = 'posted'
             ORDER BY lj.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}"
        );

        $this->render('admin/reports/ledger', [
            'pageTitle' => 'Ledger Journal',
            'journals' => $journals,
            'page' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    public function reconciliation(): void
    {
        // Verify that account balances match the sum of ledger entries
        $accounts = $this->db->fetchAll(
            "SELECT fa.id, fa.account_code, fa.account_name, fa.balance,
                    COALESCE(SUM(CASE WHEN le.entry_type = 'debit' THEN le.amount WHEN le.entry_type = 'credit' THEN -le.amount ELSE 0 END), 0) as calculated_balance
             FROM financial_accounts fa
             LEFT JOIN ledger_entries le ON fa.id = le.account_id
             LEFT JOIN ledger_journals lj ON le.journal_id = lj.id AND lj.status = 'posted'
             GROUP BY fa.id
             ORDER BY fa.account_code"
        );

        $discrepancies = [];
        foreach ($accounts as $account) {
            $cached = (int)$account['balance'];
            $calculated = (int)$account['calculated_balance'];
            if ($cached !== $calculated) {
                $discrepancies[] = [
                    'account_code' => $account['account_code'],
                    'account_name' => $account['account_name'],
                    'cached_balance' => $cached,
                    'calculated_balance' => $calculated,
                    'difference' => $cached - $calculated,
                ];
            }
        }

        // Verify double-entry balance (total debits = total credits)
        $balanceCheck = $this->db->fetchOne(
            "SELECT 
                COALESCE(SUM(CASE WHEN entry_type = 'debit' THEN amount ELSE 0 END), 0) as total_debits,
                COALESCE(SUM(CASE WHEN entry_type = 'credit' THEN amount ELSE 0 END), 0) as total_credits
             FROM ledger_entries le
             JOIN ledger_journals lj ON le.journal_id = lj.id
             WHERE lj.status = 'posted'"
        );

        $this->render('admin/reports/reconciliation', [
            'pageTitle' => 'Reconciliation Report',
            'accounts' => $accounts,
            'discrepancies' => $discrepancies,
            'totalDebits' => $balanceCheck['total_debits'] ?? '0',
            'totalCredits' => $balanceCheck['total_credits'] ?? '0',
            'isBalanced' => ($balanceCheck['total_debits'] ?? '0') === ($balanceCheck['total_credits'] ?? '0'),
        ]);
    }
}
