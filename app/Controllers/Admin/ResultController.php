<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Helpers\{Url, Flash, Csrf};

/**
 * Admin Financial Results Controller
 */
class ResultController extends BaseController
{
    public function index(): void
    {
        $results = $this->db->fetchAll(
            "SELECT frr.*, u.first_name as creator_first, u.last_name as creator_last
             FROM financial_result_records frr
             LEFT JOIN users u ON frr.created_by = u.id
             ORDER BY frr.period_end DESC"
        );

        $this->render('admin/results/index', [
            'pageTitle' => 'Financial Results',
            'results' => $results,
        ]);
    }

    public function create(): void
    {
        // Get total allocated capital
        $totalCapital = $this->db->fetchOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM capital_allocations WHERE status = 'allocated'"
        );

        $this->render('admin/results/create', [
            'pageTitle' => 'Record Financial Result',
            'totalCapital' => $totalCapital['total'] ?? '0',
        ]);
    }

    public function store(): void
    {
        if (!Csrf::validate($this->post('csrf_token'))) {
            Flash::error('Invalid request.');
            $this->redirect(Url::to('admin/results/create'));
            return;
        }

        $periodStart = $this->post('period_start');
        $periodEnd = $this->post('period_end');
        $grossResult = trim($this->post('gross_result', ''));
        $feesDeducted = trim($this->post('fees_deducted', '0'));
        $notes = trim($this->post('notes', ''));

        // Validation
        if (empty($periodStart) || empty($periodEnd)) {
            Flash::error('Period dates are required.');
            $this->redirect(Url::to('admin/results/create'));
            return;
        }

        if (!is_numeric($grossResult)) {
            Flash::error('Gross result must be a number.');
            $this->redirect(Url::to('admin/results/create'));
            return;
        }

        $feesDeducted = is_numeric($feesDeducted) ? $feesDeducted : '0';
        $netResult = (string)((int)$grossResult - (int)$feesDeducted);

        // Get total capital
        $totalCapital = $this->db->fetchOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM capital_allocations WHERE status = 'allocated'"
        );

        $this->db->query(
            "INSERT INTO financial_result_records 
                (period_start, period_end, total_capital, gross_result, fees_deducted, net_result, notes, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [$periodStart, $periodEnd, $totalCapital['total'], $grossResult, $feesDeducted, $netResult, $notes, $this->auth->userId()]
        );

        $resultId = (int)$this->db->lastInsertId();
        $this->audit('result_recorded', 'financial_result_records', $resultId, null,
            ['gross_result' => $grossResult, 'net_result' => $netResult]);

        Flash::success('Financial result recorded and pending review.');
        $this->redirect(Url::to('admin/results'));
    }

    public function approve(string $id): void
    {
        if (!Csrf::validate($this->post('csrf_token'))) {
            Flash::error('Invalid request.');
            $this->redirectBack();
            return;
        }

        $result = $this->db->fetchOne(
            "SELECT * FROM financial_result_records WHERE id = ? AND status IN ('draft', 'pending_review')",
            [$id]
        );

        if (!$result) {
            Flash::error('Result not found or already processed.');
            $this->redirectBack();
            return;
        }

        $action = $this->post('action');

        if ($action === 'approve') {
            $this->db->query(
                "UPDATE financial_result_records SET status = 'approved', reviewed_by = ?, reviewed_at = NOW(), review_notes = ? WHERE id = ?",
                [$this->auth->userId(), trim($this->post('notes', '')), $id]
            );

            // Record the result in ledger if profitable
            if ((int)$result['net_result'] > 0) {
                // Create profit ledger entries for each active allocation
                $allocations = $this->db->fetchAll(
                    "SELECT ca.*, aa.profit_share_pct
                     FROM capital_allocations ca
                     JOIN agreement_acceptances aa ON aa.user_id = ca.user_id AND aa.is_active = 1
                     WHERE ca.status = 'allocated'"
                );

                foreach ($allocations as $allocation) {
                    $sharePct = (float)$allocation['profit_share_pct'];
                    $capitalAmount = (int)$allocation['amount'];
                    $totalCapital = (int)$result['total_capital'];
                    
                    if ($totalCapital > 0) {
                        // Funder's share of profit
                        $funderShare = (int)floor(((int)$result['net_result'] * $capitalAmount / $totalCapital) * $sharePct / 100);
                    } else {
                        $funderShare = 0;
                    }

                    if ($funderShare > 0) {
                        // Create distribution calculation
                        $this->db->query(
                            "INSERT INTO distribution_calculations 
                                (result_record_id, user_id, allocation_id, capital_amount, profit_share_pct,
                                 gross_profit_share, fees_deducted, net_distribution, calculated_by)
                             VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?)",
                            [$id, $allocation['user_id'], $allocation['id'], $capitalAmount, $sharePct,
                             $funderShare, $funderShare, $this->auth->userId()]
                        );
                    }
                }
            }

            $this->audit('result_approved', 'financial_result_records', (int)$id,
                ['status' => $result['status']], ['status' => 'approved']);

            Flash::success('Financial result approved.');
        } elseif ($action === 'reject') {
            $this->db->query(
                "UPDATE financial_result_records SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW(), review_notes = ? WHERE id = ?",
                [$this->auth->userId(), trim($this->post('notes', '')), $id]
            );
            $this->audit('result_rejected', 'financial_result_records', (int)$id,
                ['status' => $result['status']], ['status' => 'rejected']);
            Flash::info('Financial result rejected.');
        }

        $this->redirect(Url::to('admin/results'));
    }
}
