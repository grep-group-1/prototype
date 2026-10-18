<?php
namespace App\Controllers;

use App\Helpers\{Url, Flash, Format};

/**
 * Statement Controller
 * 
 * Generates downloadable account statements for funders.
 */
class StatementController extends BaseController
{
    public function index(): void
    {
        $userId = $this->auth->userId();

        // Get available statement periods
        $periods = $this->db->fetchAll(
            "SELECT DISTINCT DATE_FORMAT(lj.created_at, '%Y-%m') as period
             FROM ledger_journals lj
             JOIN ledger_entries le ON lj.id = le.journal_id
             JOIN financial_accounts fa ON le.account_id = fa.id
             WHERE fa.user_id = ? AND lj.status = 'posted'
             ORDER BY period DESC",
            [$userId]
        );

        $this->render('funder/statements', [
            'pageTitle' => 'Account Statements',
            'periods' => $periods,
        ]);
    }

    public function download(): void
    {
        $userId = $this->auth->userId();
        $period = $this->get('period', date('Y-m'));
        $format = $this->get('format', 'csv');

        // Get transactions for the period
        $startDate = $period . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

        $transactions = $this->db->fetchAll(
            "SELECT lj.reference, lj.description, lj.journal_type, lj.total_amount, 
                    lj.currency, lj.created_at,
                    SUM(CASE WHEN le.entry_type = 'debit' THEN le.amount ELSE 0 END) as total_debits,
                    SUM(CASE WHEN le.entry_type = 'credit' THEN le.amount ELSE 0 END) as total_credits
             FROM ledger_journals lj
             JOIN ledger_entries le ON lj.id = le.journal_id
             JOIN financial_accounts fa ON le.account_id = fa.id
             WHERE fa.user_id = ? AND lj.status = 'posted'
               AND lj.created_at >= ? AND lj.created_at <= ?
             GROUP BY lj.id
             ORDER BY lj.created_at ASC",
            [$userId, $startDate . ' 00:00:00', $endDate . ' 23:59:59']
        );

        if ($format === 'csv') {
            $this->downloadCsv($transactions, $period);
        } else {
            Flash::error('Unsupported format.');
            $this->redirect(Url::to('statements'));
        }
    }

    private function downloadCsv(array $transactions, string $period): void
    {
        $filename = "statement_{$period}.csv";
        
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // BOM for Excel UTF-8 compatibility
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header row
        fputcsv($output, [
            'CapitalEdge Funding - Account Statement',
            'Period: ' . $period,
            'Generated: ' . date('Y-m-d H:i:s'),
        ]);
        fputcsv($output, []);
        fputcsv($output, ['Date', 'Reference', 'Description', 'Type', 'Amount (UGX)', 'Currency']);
        fputcsv($output, []);

        $totalDebits = 0;
        $totalCredits = 0;

        foreach ($transactions as $txn) {
            fputcsv($output, [
                $txn['created_at'],
                $txn['reference'],
                $txn['description'],
                ucfirst(str_replace('_', ' ', $txn['journal_type'])),
                $txn['total_amount'],
                $txn['currency'],
            ]);
            $totalDebits += $txn['total_debits'] ?? 0;
            $totalCredits += $txn['total_credits'] ?? 0;
        }

        fputcsv($output, []);
        fputcsv($output, ['', '', '', 'TOTAL DEBITS', $totalDebits]);
        fputcsv($output, ['', '', '', 'TOTAL CREDITS', $totalCredits]);
        fputcsv($output, []);
        fputcsv($output, ['This is a SIMULATED statement for testing purposes.']);
        fputcsv($output, ['No real money or transactions are represented.']);

        fclose($output);
        exit;
    }
}
