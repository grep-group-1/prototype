<?php
namespace App\Controllers;

use App\Helpers\{Url, Flash, Csrf};
use App\Services\PaymentGateway;

/**
 * Deposit Controller
 * 
 * Handles simulated deposit workflow for funders.
 */
class DepositController extends BaseController
{
    public function index(): void
    {
        $userId = $this->auth->userId();

        $deposits = $this->db->fetchAll(
            "SELECT * FROM payment_transactions 
             WHERE user_id = ? 
             ORDER BY created_at DESC",
            [$userId]
        );

        $this->render('funder/deposits', [
            'pageTitle' => 'Deposits',
            'deposits' => $deposits,
        ]);
    }

    public function create(): void
    {
        // Check if user has accepted an agreement
        $userId = $this->auth->userId();
        $hasAgreement = $this->db->fetchOne(
            "SELECT id FROM agreement_acceptances WHERE user_id = ? AND is_active = 1",
            [$userId]
        );

        // Check KYC status
        $kycCase = $this->db->fetchOne(
            "SELECT status FROM kyc_cases WHERE user_id = ? ORDER BY created_at DESC LIMIT 1",
            [$userId]
        );
        $kycApproved = $kycCase && $kycCase['status'] === 'approved';

        $this->render('funder/deposit-create', [
            'pageTitle' => 'New Deposit',
            'hasAgreement' => $hasAgreement,
            'kycApproved' => $kycApproved,
        ]);
    }

    public function store(): void
    {
        if (!Csrf::validate($this->post('csrf_token'))) {
            Flash::error('Invalid request.');
            $this->redirect(Url::to('deposits/new'));
            return;
        }

        $userId = $this->auth->userId();
        $amount = trim($this->post('amount', ''));
        $paymentMethod = $this->post('payment_method', 'test_gateway');

        // Validate amount
        if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
            Flash::error('Please enter a valid deposit amount greater than zero.');
            $this->redirect(Url::to('deposits/new'));
            return;
        }

        // Minimum deposit check
        if ($amount < 100000) {
            Flash::error('Minimum deposit amount is UGX 100,000.');
            $this->redirect(Url::to('deposits/new'));
            return;
        }

        // Maximum deposit check (simulated limit)
        if ($amount > 1000000000) {
            Flash::error('Maximum deposit amount is UGX 1,000,000,000.');
            $this->redirect(Url::to('deposits/new'));
            return;
        }

        // Generate idempotency key to prevent duplicates
        $idempotencyKey = bin2hex(random_bytes(16));
        $reference = $this->generateReference('DEP');

        // Process through simulated payment gateway
        $gateway = new PaymentGateway();
        $result = $gateway->processDeposit([
            'user_id' => $userId,
            'amount' => $amount,
            'currency' => 'UGX',
            'reference' => $reference,
            'idempotency_key' => $idempotencyKey,
            'payment_method' => $paymentMethod,
        ]);

        if ($result['success']) {
            $this->audit('deposit_initiated', 'payment_transaction', $result['transaction_id'],
                null, ['amount' => $amount, 'reference' => $reference]);

            $this->notify($userId, 'deposit_pending', 'Deposit Initiated',
                "Your test deposit of UGX " . number_format($amount) . " has been initiated. Reference: {$reference}");

            Flash::success("Deposit initiated successfully. Reference: {$reference}");
            $this->redirect(Url::to("deposits/{$result['transaction_id']}"));
        } else {
            Flash::error($result['message'] ?? 'Deposit failed. Please try again.');
            $this->redirect(Url::to('deposits/new'));
        }
    }

    public function show(string $id): void
    {
        $userId = $this->auth->userId();

        $transaction = $this->db->fetchOne(
            "SELECT * FROM payment_transactions WHERE id = ? AND user_id = ?",
            [$id, $userId]
        );

        if (!$transaction) {
            Flash::error('Transaction not found.');
            $this->redirect(Url::to('deposits'));
            return;
        }

        // Get related ledger entries
        $ledgerEntries = $this->db->fetchAll(
            "SELECT lj.*, le.entry_type, le.amount as entry_amount
             FROM ledger_journals lj
             JOIN ledger_entries le ON lj.id = le.journal_id
             JOIN financial_accounts fa ON le.account_id = fa.id
             WHERE lj.related_entity_type = 'payment_transaction' AND lj.related_entity_id = ?
             AND fa.user_id = ?",
            [$id, $userId]
        );

        $this->render('funder/deposit-show', [
            'pageTitle' => 'Deposit Details',
            'transaction' => $transaction,
            'ledgerEntries' => $ledgerEntries,
        ]);
    }
}
