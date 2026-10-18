<?php
namespace App\Controllers;

use App\Helpers\{Url, Flash, Format};

/**
 * Transaction Controller
 * 
 * Displays transaction history for the authenticated funder.
 */
class TransactionController extends BaseController
{
    public function index(): void
    {
        $userId = $this->auth->userId();
        $page = max(1, (int)($this->get('page', 1)));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        // Filter parameters
        $type = $this->get('type', '');
        $dateFrom = $this->get('date_from', '');
        $dateTo = $this->get('date_to', '');

        $where = ['fa.user_id = ?'];
        $params = [$userId];

        if ($type) {
            $where[] = 'lj.journal_type = ?';
            $params[] = $type;
        }
        if ($dateFrom) {
            $where[] = 'lj.created_at >= ?';
            $params[] = $dateFrom . ' 00:00:00';
        }
        if ($dateTo) {
            $where[] = 'lj.created_at <= ?';
            $params[] = $dateTo . ' 23:59:59';
        }

        $whereClause = implode(' AND ', $where);

        // Get total count
        $countResult = $this->db->fetchOne(
            "SELECT COUNT(DISTINCT lj.id) as total
             FROM ledger_journals lj
             JOIN ledger_entries le ON lj.id = le.journal_id
             JOIN financial_accounts fa ON le.account_id = fa.id
             WHERE {$whereClause}",
            $params
        );
        $total = (int)$countResult['total'];
        $totalPages = max(1, ceil($total / $perPage));

        // Get paginated results
        $transactions = $this->db->fetchAll(
            "SELECT lj.id, lj.reference, lj.description, lj.journal_type, lj.total_amount, 
                    lj.currency, lj.created_at
             FROM ledger_journals lj
             JOIN ledger_entries le ON lj.id = le.journal_id
             JOIN financial_accounts fa ON le.account_id = fa.id
             WHERE {$whereClause} AND lj.status = 'posted'
             GROUP BY lj.id
             ORDER BY lj.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        $this->render('funder/transactions', [
            'pageTitle' => 'Transaction History',
            'transactions' => $transactions,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'filters' => ['type' => $type, 'date_from' => $dateFrom, 'date_to' => $dateTo],
        ]);
    }

    public function show(string $reference): void
    {
        $userId = $this->auth->userId();

        $journal = $this->db->fetchOne(
            "SELECT lj.*
             FROM ledger_journals lj
             JOIN ledger_entries le ON lj.id = le.journal_id
             JOIN financial_accounts fa ON le.account_id = fa.id
             WHERE lj.reference = ? AND fa.user_id = ?
             LIMIT 1",
            [$reference, $userId]
        );

        if (!$journal) {
            Flash::error('Transaction not found.');
            $this->redirect(Url::to('transactions'));
            return;
        }

        $entries = $this->db->fetchAll(
            "SELECT le.*, fa.account_code, fa.account_name, fa.user_id
             FROM ledger_entries le
             JOIN financial_accounts fa ON le.account_id = fa.id
             WHERE le.journal_id = ?",
            [$journal['id']]
        );

        $this->render('funder/transaction-show', [
            'pageTitle' => 'Transaction Details',
            'journal' => $journal,
            'entries' => $entries,
        ]);
    }
}
