<?php
namespace App\Controllers;

use App\Helpers\{Url, Flash, Csrf};
use App\Services\PaymentGateway;

/**
 * Withdrawal Controller
 * 
 * Handles withdrawal requests for funders.
 */
class WithdrawalController extends BaseController
{
    public function index(): void
    {
        $userId = $this->auth->userId();

        $withdrawals = $this->db->fetchAll(
            "SELECT * FROM withdrawal_requests WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        );

        // Get withdrawable balance
        $account = $this->db->fetchOne(
            "SELECT balance FROM financial_accounts WHERE user_id = ? AND account_code LIKE 'USR%_WITHDRAWABLE'",
            [$userId]
        );

        $this->render('funder/withdrawals', [
            'pageTitle' => 'Withdrawals',
            'withdrawals' => $withdrawals,
            'withdrawableBalance' => $account ? $account['balance'] : '0',
        ]);
    }

    public function create(): void
    {
        $userId = $this->auth->userId();

        // Get withdrawable balance
        $account = $this->db->fetchOne(
            "SELECT balance FROM financial_accounts WHERE user_id = ? AND account_code LIKE 'USR%_WITHDRAWABLE'",
            [$userId]
        );

        // Check active agreement for withdrawal conditions
        $agreement = $this->db->fetchOne(
            "SELECT aa.*, fp.code as product_code, fp.min_lock_in_days, fp.withdrawal_notice_days
             FROM agreement_acceptances aa
             JOIN funding_products fp ON aa.product_id = fp.id
             WHERE aa.user_id = ? AND aa.is_active = 1
             ORDER BY aa.accepted_at DESC LIMIT 1",
            [$userId]
        );

        $this->render('funder/withdrawal-create', [
            'pageTitle' => 'Request Withdrawal',
            'withdrawableBalance' => $account ? $account['balance'] : '0',
            'agreement' => $agreement,
        ]);
    }

    public function store(): void
    {
        if (!Csrf::validate($this->post('csrf_token'))) {
            Flash::error('Invalid request.');
            $this->redirect(Url::to('withdrawals/new'));
            return;
        }

        $userId = $this->auth->userId();
        $amount = trim($this->post('amount', ''));

        // Validate amount
        if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
            Flash::error('Please enter a valid withdrawal amount.');
            $this->redirect(Url::to('withdrawals/new'));
            return;
        }

        // Get withdrawable balance
        $account = $this->db->fetchOne(
            "SELECT balance FROM financial_accounts WHERE user_id = ? AND account_code LIKE 'USR%_WITHDRAWABLE'",
            [$userId]
        );

        $balance = $account ? (int)$account['balance'] : 0;

        if ($amount > $balance) {
            Flash::error('Insufficient withdrawable balance.');
            $this->redirect(Url::to('withdrawals/new'));
            return;
        }

        // Process withdrawal
        $reference = $this->generateReference('WD');
        $idempotencyKey = bin2hex(random_bytes(16));

        $gateway = new PaymentGateway();
        $result = $gateway->processWithdrawal([
            'user_id' => $userId,
            'amount' => $amount,
            'currency' => 'UGX',
            'reference' => $reference,
            'idempotency_key' => $idempotencyKey,
        ]);

        if ($result['success']) {
            $this->audit('withdrawal_requested', 'withdrawal_request', $result['withdrawal_id'],
                null, ['amount' => $amount, 'reference' => $reference]);

            $this->notify($userId, 'withdrawal_pending', 'Withdrawal Request Submitted',
                "Your withdrawal request of UGX " . number_format($amount) . " has been submitted. Reference: {$reference}");

            Flash::success("Withdrawal request submitted. Reference: {$reference}");
            $this->redirect(Url::to('withdrawals'));
        } else {
            Flash::error($result['message'] ?? 'Withdrawal request failed.');
            $this->redirect(Url::to('withdrawals/new'));
        }
    }

    public function show(string $id): void
    {
        $userId = $this->auth->userId();

        $withdrawal = $this->db->fetchOne(
            "SELECT * FROM withdrawal_requests WHERE id = ? AND user_id = ?",
            [$id, $userId]
        );

        if (!$withdrawal) {
            Flash::error('Withdrawal not found.');
            $this->redirect(Url::to('withdrawals'));
            return;
        }

        $this->render('funder/withdrawal-show', [
            'pageTitle' => 'Withdrawal Details',
            'withdrawal' => $withdrawal,
        ]);
    }
}
