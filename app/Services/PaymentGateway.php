<?php
namespace App\Services;

/**
 * Simulated Payment Gateway
 * 
 * Mock payment processor for local testing.
 * Simulates deposit and withdrawal processing without real money.
 * 
 * IMPORTANT: This is for demonstration only. No real money moves.
 */
class PaymentGateway
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Process a simulated deposit.
     */
    public function processDeposit(array $data): array
    {
        // Check for idempotency (prevent duplicate processing)
        if (!empty($data['idempotency_key'])) {
            $existing = $this->db->fetchOne(
                "SELECT id FROM payment_transactions WHERE idempotency_key = ?",
                [$data['idempotency_key']]
            );
            if ($existing) {
                return [
                    'success' => false,
                    'message' => 'Duplicate deposit request detected.',
                ];
            }
        }

        try {
            $this->db->beginTransaction();

            // Create payment transaction record
            $this->db->query(
                "INSERT INTO payment_transactions 
                    (user_id, reference, amount, currency, status, payment_method, idempotency_key)
                 VALUES (?, ?, ?, ?, 'pending', ?, ?)",
                [
                    $data['user_id'],
                    $data['reference'],
                    $data['amount'],
                    $data['currency'],
                    $data['payment_method'],
                    $data['idempotency_key'] ?? null,
                ]
            );

            $transactionId = (int) $this->db->lastInsertId();

            // Simulate gateway processing (always succeeds in test mode)
            $gatewayRef = 'GW-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));

            // Update transaction to completed
            $this->db->query(
                "UPDATE payment_transactions 
                 SET status = 'completed', gateway_reference = ?, completed_at = NOW(),
                     status_history = ?
                 WHERE id = ?",
                [
                    $gatewayRef,
                    json_encode([
                        ['status' => 'pending', 'at' => date('Y-m-d H:i:s')],
                        ['status' => 'completed', 'at' => date('Y-m-d H:i:s')],
                    ]),
                    $transactionId,
                ]
            );

            // Record in the ledger
            $journalRef = 'JRN-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));

            $this->db->query(
                "INSERT INTO ledger_journals 
                    (reference, description, journal_type, total_amount, currency, status,
                     related_entity_type, related_entity_id, created_by)
                 VALUES (?, ?, 'test_deposit', ?, ?, 'posted', 'payment_transaction', ?, ?)",
                [
                    $journalRef,
                    "Test deposit: {$data['reference']}",
                    $data['amount'],
                    $data['currency'],
                    $transactionId,
                    $data['user_id'],
                ]
            );

            $journalId = (int) $this->db->lastInsertId();

            // Get user's deposit account
            $depositAccount = $this->db->fetchOne(
                "SELECT id FROM financial_accounts WHERE user_id = ? AND account_code LIKE 'USR%_DEPOSIT'",
                [$data['user_id']]
            );

            // Get system deposit holding account
            $holdingAccount = $this->db->fetchOne(
                "SELECT id FROM financial_accounts WHERE account_code = 'SYS_DEPOSIT_HOLDING'"
            );

            if ($depositAccount && $holdingAccount) {
                // Double entry: Debit deposit wallet, Credit holding account
                $this->db->query(
                    "INSERT INTO ledger_entries (journal_id, account_id, entry_type, amount, currency)
                     VALUES (?, ?, 'debit', ?, ?)",
                    [$journalId, $depositAccount['id'], $data['amount'], $data['currency']]
                );
                $this->db->query(
                    "INSERT INTO ledger_entries (journal_id, account_id, entry_type, amount, currency)
                     VALUES (?, ?, 'credit', ?, ?)",
                    [$journalId, $holdingAccount['id'], $data['amount'], $data['currency']]
                );

                // Update cached balances
                $this->db->query(
                    "UPDATE financial_accounts SET balance = balance + ? WHERE id = ?",
                    [$data['amount'], $depositAccount['id']]
                );
                $this->db->query(
                    "UPDATE financial_accounts SET balance = balance + ? WHERE id = ?",
                    [$data['amount'], $holdingAccount['id']]
                );
            }

            $this->db->commit();

            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'reference' => $data['reference'],
                'gateway_reference' => $gatewayRef,
                'message' => 'Deposit processed successfully (simulated).',
            ];

        } catch (\Exception $e) {
            $this->db->rollback();
            return [
                'success' => false,
                'message' => 'Deposit processing failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Process a simulated withdrawal.
     */
    public function processWithdrawal(array $data): array
    {
        // Check for idempotency
        if (!empty($data['idempotency_key'])) {
            $existing = $this->db->fetchOne(
                "SELECT id FROM withdrawal_requests WHERE idempotency_key = ?",
                [$data['idempotency_key']]
            );
            if ($existing) {
                return [
                    'success' => false,
                    'message' => 'Duplicate withdrawal request detected.',
                ];
            }
        }

        try {
            $this->db->beginTransaction();

            $reference = $data['reference'];
            $idempotencyKey = $data['idempotency_key'] ?? bin2hex(random_bytes(16));

            $this->db->query(
                "INSERT INTO withdrawal_requests 
                    (user_id, reference, amount, currency, status, payment_method)
                 VALUES (?, ?, ?, ?, 'pending', ?)",
                [
                    $data['user_id'],
                    $reference,
                    $data['amount'],
                    $data['currency'],
                    $data['payment_method'] ?? 'bank_transfer',
                ]
            );

            $withdrawalId = (int) $this->db->lastInsertId();

            // Record pending withdrawal in ledger
            $journalRef = 'JRN-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));

            $this->db->query(
                "INSERT INTO ledger_journals 
                    (reference, description, journal_type, total_amount, currency, status,
                     related_entity_type, related_entity_id, created_by)
                 VALUES (?, ?, 'withdrawal_request', ?, ?, 'posted', 'withdrawal_request', ?, ?)",
                [
                    $journalRef,
                    "Withdrawal request: {$reference}",
                    $data['amount'],
                    $data['currency'],
                    $withdrawalId,
                    $data['user_id'],
                ]
            );

            $journalId = (int) $this->db->lastInsertId();

            // Get user's withdrawable account
            $withdrawableAccount = $this->db->fetchOne(
                "SELECT id FROM financial_accounts WHERE user_id = ? AND account_code LIKE 'USR%_WITHDRAWABLE'",
                [$data['user_id']]
            );

            $pendingAccount = $this->db->fetchOne(
                "SELECT id FROM financial_accounts WHERE account_code = 'SYS_WITHDRAWAL_PENDING'"
            );

            if ($withdrawableAccount && $pendingAccount) {
                // Debit withdrawable, credit pending
                $this->db->query(
                    "INSERT INTO ledger_entries (journal_id, account_id, entry_type, amount, currency)
                     VALUES (?, ?, 'debit', ?, ?)",
                    [$journalId, $withdrawableAccount['id'], $data['amount'], $data['currency']]
                );
                $this->db->query(
                    "INSERT INTO ledger_entries (journal_id, account_id, entry_type, amount, currency)
                     VALUES (?, ?, 'credit', ?, ?)",
                    [$journalId, $pendingAccount['id'], $data['amount'], $data['currency']]
                );

                // Update cached balances
                $this->db->query(
                    "UPDATE financial_accounts SET balance = balance - ? WHERE id = ?",
                    [$data['amount'], $withdrawableAccount['id']]
                );
                $this->db->query(
                    "UPDATE financial_accounts SET balance = balance + ? WHERE id = ?",
                    [$data['amount'], $pendingAccount['id']]
                );
            }

            $this->db->commit();

            return [
                'success' => true,
                'withdrawal_id' => $withdrawalId,
                'reference' => $reference,
                'message' => 'Withdrawal request submitted successfully.',
            ];

        } catch (\Exception $e) {
            $this->db->rollback();
            return [
                'success' => false,
                'message' => 'Withdrawal processing failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Simulate withdrawal completion (called by admin).
     */
    public function completeWithdrawal(int $withdrawalId): array
    {
        try {
            $this->db->beginTransaction();

            $withdrawal = $this->db->fetchOne(
                "SELECT * FROM withdrawal_requests WHERE id = ? AND status IN ('pending', 'approved', 'processing')",
                [$withdrawalId]
            );

            if (!$withdrawal) {
                $this->db->rollback();
                return ['success' => false, 'message' => 'Withdrawal not found or already processed.'];
            }

            $gatewayRef = 'GW-WD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));

            // Update withdrawal status
            $this->db->query(
                "UPDATE withdrawal_requests SET status = 'completed', payment_reference = ?, processed_at = NOW() WHERE id = ?",
                [$gatewayRef, $withdrawalId]
            );

            // Record completion in ledger
            $journalRef = 'JRN-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));

            $this->db->query(
                "INSERT INTO ledger_journals 
                    (reference, description, journal_type, total_amount, currency, status,
                     related_entity_type, related_entity_id, created_by)
                 VALUES (?, ?, 'withdrawal_payment', ?, ?, 'posted', 'withdrawal_request', ?, ?)",
                [
                    $journalRef,
                    "Withdrawal payment: {$withdrawal['reference']}",
                    $withdrawal['amount'],
                    $withdrawal['currency'],
                    $withdrawalId,
                    $withdrawal['user_id'],
                ]
            );

            $journalId = (int) $this->db->lastInsertId();

            // Move from pending to operating (money leaves the system)
            $pendingAccount = $this->db->fetchOne(
                "SELECT id FROM financial_accounts WHERE account_code = 'SYS_WITHDRAWAL_PENDING'"
            );
            $operatingAccount = $this->db->fetchOne(
                "SELECT id FROM financial_accounts WHERE account_code = 'SYS_OPERATING'"
            );

            if ($pendingAccount && $operatingAccount) {
                $this->db->query(
                    "INSERT INTO ledger_entries (journal_id, account_id, entry_type, amount, currency)
                     VALUES (?, ?, 'debit', ?, ?)",
                    [$journalId, $operatingAccount['id'], $withdrawal['amount'], $withdrawal['currency']]
                );
                $this->db->query(
                    "INSERT INTO ledger_entries (journal_id, account_id, entry_type, amount, currency)
                     VALUES (?, ?, 'credit', ?, ?)",
                    [$journalId, $pendingAccount['id'], $withdrawal['amount'], $withdrawal['currency']]
                );

                $this->db->query(
                    "UPDATE financial_accounts SET balance = balance - ? WHERE id = ?",
                    [$withdrawal['amount'], $pendingAccount['id']]
                );
                $this->db->query(
                    "UPDATE financial_accounts SET balance = balance + ? WHERE id = ?",
                    [$withdrawal['amount'], $operatingAccount['id']]
                );
            }

            $this->db->commit();

            return [
                'success' => true,
                'gateway_reference' => $gatewayRef,
                'message' => 'Withdrawal completed (simulated).',
            ];

        } catch (\Exception $e) {
            $this->db->rollback();
            return [
                'success' => false,
                'message' => 'Withdrawal completion failed: ' . $e->getMessage(),
            ];
        }
    }
}
