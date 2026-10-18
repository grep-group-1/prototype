<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Helpers\{Url, Flash, Csrf};

/**
 * Admin Distribution Controller
 */
class DistributionController extends BaseController
{
    public function index(): void
    {
        $distributions = $this->db->fetchAll(
            "SELECT dc.*, u.first_name, u.last_name, u.email,
                    frr.period_start, frr.period_end
             FROM distribution_calculations dc
             JOIN users u ON dc.user_id = u.id
             JOIN financial_result_records frr ON dc.result_record_id = frr.id
             ORDER BY dc.created_at DESC"
        );

        $this->render('admin/distributions/index', [
            'pageTitle' => 'Distributions',
            'distributions' => $distributions,
        ]);
    }

    public function approve(string $id): void
    {
        if (!Csrf::validate($this->post('csrf_token'))) {
            Flash::error('Invalid request.');
            $this->redirectBack();
            return;
        }

        $distribution = $this->db->fetchOne(
            "SELECT * FROM distribution_calculations WHERE id = ? AND status = 'calculated'",
            [$id]
        );

        if (!$distribution) {
            Flash::error('Distribution not found or already processed.');
            $this->redirectBack();
            return;
        }

        $action = $this->post('action');

        if ($action === 'approve') {
            $this->db->query(
                "UPDATE distribution_calculations SET status = 'approved', approved_by = ?, approved_at = NOW() WHERE id = ?",
                [$this->auth->userId(), $id]
            );

            // Credit the user's distribution balance
            $distAccount = $this->db->fetchOne(
                "SELECT id FROM financial_accounts WHERE user_id = ? AND account_code LIKE 'USR%_DISTRIBUTION'",
                [$distribution['user_id']]
            );
            $distPayable = $this->db->fetchOne(
                "SELECT id FROM financial_accounts WHERE account_code = 'SYS_DISTRIBUTION_PAYABLE'"
            );

            if ($distAccount && $distPayable) {
                $journalRef = 'JRN-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
                $amount = $distribution['net_distribution'];

                $this->db->query(
                    "INSERT INTO ledger_journals (reference, description, journal_type, total_amount, currency, status, related_entity_type, related_entity_id, created_by)
                     VALUES (?, ?, 'distribution_accrual', ?, 'UGX', 'posted', 'distribution_calculations', ?, ?)",
                    [$journalRef, "Distribution accrual for user #{$distribution['user_id']}", $amount, $id, $this->auth->userId()]
                );

                $journalId = (int)$this->db->lastInsertId();

                $this->db->query(
                    "INSERT INTO ledger_entries (journal_id, account_id, entry_type, amount, currency) VALUES (?, ?, 'credit', ?, 'UGX')",
                    [$journalId, $distAccount['id'], $amount]
                );
                $this->db->query(
                    "INSERT INTO ledger_entries (journal_id, account_id, entry_type, amount, currency) VALUES (?, ?, 'debit', ?, 'UGX')",
                    [$journalId, $distPayable['id'], $amount]
                );

                // Update balances
                $this->db->query("UPDATE financial_accounts SET balance = balance + ? WHERE id = ?", [$amount, $distAccount['id']]);
                $this->db->query("UPDATE financial_accounts SET balance = balance + ? WHERE id = ?", [$amount, $distPayable['id']]);

                // Also credit the withdrawable balance
                $withdrawableAccount = $this->db->fetchOne(
                    "SELECT id FROM financial_accounts WHERE user_id = ? AND account_code LIKE 'USR%_WITHDRAWABLE'",
                    [$distribution['user_id']]
                );
                if ($withdrawableAccount) {
                    $this->db->query("UPDATE financial_accounts SET balance = balance + ? WHERE id = ?", [$amount, $withdrawableAccount['id']]);
                }
            }

            // Create distribution payment record
            $this->db->query(
                "INSERT INTO distribution_payments (distribution_calc_id, amount, currency, status)
                 VALUES (?, ?, 'UGX', 'pending')",
                [$id, $distribution['net_distribution']]
            );

            // Update calculation status
            $this->db->query("UPDATE distribution_calculations SET status = 'paid' WHERE id = ?", [$id]);

            $this->notify($distribution['user_id'], 'distribution_paid', 'Distribution Processed',
                "A profit distribution of UGX " . number_format($distribution['net_distribution']) . " has been credited to your account.",
                'distribution_calculations', (int)$id);

            $this->audit('distribution_approved', 'distribution_calculations', (int)$id,
                ['status' => 'calculated'], ['status' => 'paid']);

            Flash::success('Distribution approved and credited.');
        }

        $this->redirect(Url::to('admin/distributions'));
    }
}
