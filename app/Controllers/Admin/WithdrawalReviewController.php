<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Helpers\{Url, Flash, Csrf};
use App\Services\PaymentGateway;

/**
 * Admin Withdrawal Review Controller
 */
class WithdrawalReviewController extends BaseController
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
            $where .= ' AND wr.status = ?';
            $params[] = $status;
        }

        $withdrawals = $this->db->fetchAll(
            "SELECT wr.*, u.first_name, u.last_name, u.email
             FROM withdrawal_requests wr
             JOIN users u ON wr.user_id = u.id
             WHERE {$where}
             ORDER BY wr.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        $total = $this->db->fetchOne(
            "SELECT COUNT(*) as cnt FROM withdrawal_requests wr WHERE {$where}", $params
        );
        $totalPages = max(1, ceil($total['cnt'] / $perPage));

        $this->render('admin/withdrawals/index', [
            'pageTitle' => 'Withdrawal Requests',
            'withdrawals' => $withdrawals,
            'page' => $page,
            'totalPages' => $totalPages,
            'statusFilter' => $status,
        ]);
    }

    public function process(string $id): void
    {
        if (!Csrf::validate($this->post('csrf_token'))) {
            Flash::error('Invalid request.');
            $this->redirectBack();
            return;
        }

        $withdrawal = $this->db->fetchOne(
            "SELECT * FROM withdrawal_requests WHERE id = ? AND status IN ('pending', 'approved')",
            [$id]
        );

        if (!$withdrawal) {
            Flash::error('Withdrawal not found or already processed.');
            $this->redirectBack();
            return;
        }

        $action = $this->post('action');

        if ($action === 'approve') {
            // Mark as approved
            $this->db->query(
                "UPDATE withdrawal_requests SET status = 'approved', approved_by = ?, approved_at = NOW() WHERE id = ?",
                [$this->auth->userId(), $id]
            );
            $this->audit('withdrawal_approved', 'withdrawal_requests', (int)$id);

            $this->notify($withdrawal['user_id'], 'withdrawal_approved', 'Withdrawal Approved',
                "Your withdrawal request of UGX " . number_format($withdrawal['amount']) . " has been approved.",
                'withdrawal_request', (int)$id);

            Flash::success('Withdrawal approved.');
        } elseif ($action === 'complete') {
            // Process the payment
            $gateway = new PaymentGateway();
            $result = $gateway->completeWithdrawal((int)$id);

            if ($result['success']) {
                $this->db->query(
                    "UPDATE withdrawal_requests SET status = 'completed' WHERE id = ?", [$id]
                );

                $this->audit('withdrawal_completed', 'withdrawal_requests', (int)$id,
                    ['status' => $withdrawal['status']], ['status' => 'completed']);

                $this->notify($withdrawal['user_id'], 'withdrawal_completed', 'Withdrawal Completed',
                    "Your withdrawal of UGX " . number_format($withdrawal['amount']) . " has been processed.",
                    'withdrawal_request', (int)$id);

                Flash::success('Withdrawal completed (simulated).');
            } else {
                Flash::error($result['message']);
            }
        } elseif ($action === 'reject') {
            $reason = trim($this->post('reason', ''));

            // Reverse the pending ledger entries
            $this->db->query(
                "UPDATE withdrawal_requests SET status = 'rejected', rejection_reason = ? WHERE id = ?",
                [$reason, $id]
            );

            // Reverse: move from pending back to withdrawable
            $withdrawableAccount = $this->db->fetchOne(
                "SELECT id FROM financial_accounts WHERE user_id = ? AND account_code LIKE 'USR%_WITHDRAWABLE'",
                [$withdrawal['user_id']]
            );
            $pendingAccount = $this->db->fetchOne(
                "SELECT id FROM financial_accounts WHERE account_code = 'SYS_WITHDRAWAL_PENDING'"
            );

            if ($withdrawableAccount && $pendingAccount) {
                $journalRef = 'JRN-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
                $amount = $withdrawal['amount'];

                $this->db->query(
                    "INSERT INTO ledger_journals (reference, description, journal_type, total_amount, currency, status, related_entity_type, related_entity_id, created_by)
                     VALUES (?, ?, 'reversal', ?, 'UGX', 'posted', 'withdrawal_request', ?, ?)",
                    [$journalRef, "Withdrawal reversal: {$withdrawal['reference']}", $amount, $id, $this->auth->userId()]
                );

                $journalId = (int)$this->db->lastInsertId();

                $this->db->query(
                    "INSERT INTO ledger_entries (journal_id, account_id, entry_type, amount, currency) VALUES (?, ?, 'debit', ?, 'UGX')",
                    [$journalId, $withdrawableAccount['id'], $amount]
                );
                $this->db->query(
                    "INSERT INTO ledger_entries (journal_id, account_id, entry_type, amount, currency) VALUES (?, ?, 'credit', ?, 'UGX')",
                    [$journalId, $pendingAccount['id'], $amount]
                );

                $this->db->query("UPDATE financial_accounts SET balance = balance + ? WHERE id = ?", [$amount, $withdrawableAccount['id']]);
                $this->db->query("UPDATE financial_accounts SET balance = balance - ? WHERE id = ?", [$amount, $pendingAccount['id']]);
            }

            $this->audit('withdrawal_rejected', 'withdrawal_requests', (int)$id,
                ['status' => 'pending'], ['status' => 'rejected']);

            $this->notify($withdrawal['user_id'], 'withdrawal_rejected', 'Withdrawal Rejected',
                "Your withdrawal request has been rejected." . ($reason ? " Reason: {$reason}" : ''),
                'withdrawal_request', (int)$id);

            Flash::info('Withdrawal rejected and funds returned.');
        }

        $this->redirect(Url::to('admin/withdrawals'));
    }
}
