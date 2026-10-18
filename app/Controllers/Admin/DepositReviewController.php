<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Helpers\{Url, Flash, Csrf};

/**
 * Admin Deposit Review Controller
 */
class DepositReviewController extends BaseController
{
    public function index(): void
    {
        $status = $this->get('status', '');
        $page = max(1, (int)($this->get('page', 1)));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $where = '1=1';
        $params = [];
        if ($status) {
            $where .= ' AND pt.status = ?';
            $params[] = $status;
        }

        $total = $this->db->fetchOne(
            "SELECT COUNT(*) as cnt FROM payment_transactions pt WHERE {$where}", $params
        );
        $totalPages = max(1, ceil($total['cnt'] / $perPage));

        $deposits = $this->db->fetchAll(
            "SELECT pt.*, u.first_name, u.last_name, u.email
             FROM payment_transactions pt
             JOIN users u ON pt.user_id = u.id
             WHERE {$where}
             ORDER BY pt.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        $this->render('admin/deposits/index', [
            'pageTitle' => 'Deposit Review',
            'deposits' => $deposits,
            'page' => $page,
            'totalPages' => $totalPages,
            'statusFilter' => $status,
        ]);
    }

    public function confirm(string $id): void
    {
        if (!Csrf::validate($this->post('csrf_token'))) {
            Flash::error('Invalid request.');
            $this->redirectBack();
            return;
        }

        $transaction = $this->db->fetchOne(
            "SELECT * FROM payment_transactions WHERE id = ? AND status = 'completed'",
            [$id]
        );

        if (!$transaction) {
            Flash::error('Transaction not found or not in completed status.');
            $this->redirectBack();
            return;
        }

        $action = $this->post('action'); // allocate or reject

        if ($action === 'allocate') {
            // Check if already allocated
            $existing = $this->db->fetchOne(
                "SELECT id FROM capital_allocations WHERE payment_id = ?", [$id]
            );

            if ($existing) {
                Flash::warning('This deposit has already been allocated.');
                $this->redirectBack();
                return;
            }

            // Create capital allocation
            $this->db->query(
                "INSERT INTO capital_allocations (user_id, payment_id, amount, currency, created_by)
                 VALUES (?, ?, ?, ?, ?)",
                [
                    $transaction['user_id'],
                    $id,
                    $transaction['amount'],
                    $transaction['currency'],
                    $this->auth->userId(),
                ]
            );

            $allocationId = (int) $this->db->lastInsertId();

            // Record allocation in ledger
            $depositAccount = $this->db->fetchOne(
                "SELECT id FROM financial_accounts WHERE user_id = ? AND account_code LIKE 'USR%_DEPOSIT'",
                [$transaction['user_id']]
            );
            $capitalAccount = $this->db->fetchOne(
                "SELECT id FROM financial_accounts WHERE user_id = ? AND account_code LIKE 'USR%_CAPITAL'",
                [$transaction['user_id']]
            );
            $capitalPool = $this->db->fetchOne(
                "SELECT id FROM financial_accounts WHERE account_code = 'SYS_CAPITAL_POOL'"
            );

            if ($depositAccount && $capitalAccount && $capitalPool) {
                $journalRef = 'JRN-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));

                $this->db->query(
                    "INSERT INTO ledger_journals (reference, description, journal_type, total_amount, currency, status, related_entity_type, related_entity_id, created_by)
                     VALUES (?, ?, 'capital_allocation', ?, ?, 'posted', 'capital_allocations', ?, ?)",
                    [$journalRef, "Capital allocation for deposit #{$id}", $transaction['amount'], $transaction['currency'], $allocationId, $this->auth->userId()]
                );

                $journalId = (int) $this->db->lastInsertId();

                // Debit capital account, Credit deposit account (move from deposit to capital)
                $this->db->query(
                    "INSERT INTO ledger_entries (journal_id, account_id, entry_type, amount, currency) VALUES (?, ?, 'debit', ?, ?)",
                    [$journalId, $capitalAccount['id'], $transaction['amount'], $transaction['currency']]
                );
                $this->db->query(
                    "INSERT INTO ledger_entries (journal_id, account_id, entry_type, amount, currency) VALUES (?, ?, 'credit', ?, ?)",
                    [$journalId, $depositAccount['id'], $transaction['amount'], $transaction['currency']]
                );

                // Update balances
                $this->db->query("UPDATE financial_accounts SET balance = balance + ? WHERE id = ?", [$transaction['amount'], $capitalAccount['id']]);
                $this->db->query("UPDATE financial_accounts SET balance = balance - ? WHERE id = ?", [$transaction['amount'], $depositAccount['id']]);
            }

            $this->audit('capital_allocated', 'capital_allocations', $allocationId, null,
                ['amount' => $transaction['amount'], 'payment_id' => (int)$id]);

            $this->notify($transaction['user_id'], 'capital_allocated', 'Capital Allocated',
                "Your deposit of UGX " . number_format($transaction['amount']) . " has been allocated to the funding arrangement.",
                'capital_allocation', $allocationId);

            Flash::success('Capital allocated successfully.');
        } else {
            Flash::info('Action not recognized.');
        }

        $this->redirect(Url::to('admin/deposits'));
    }
}
