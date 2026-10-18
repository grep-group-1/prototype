<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Helpers\{Url, Flash, Csrf};

/**
 * Admin KYC Review Controller
 */
class KycReviewController extends BaseController
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
            $where .= ' AND kc.status = ?';
            $params[] = $status;
        }

        $total = $this->db->fetchOne(
            "SELECT COUNT(*) as cnt FROM kyc_cases kc WHERE {$where}", $params
        );
        $totalPages = max(1, ceil($total['cnt'] / $perPage));

        $cases = $this->db->fetchAll(
            "SELECT kc.*, u.first_name, u.last_name, u.email
             FROM kyc_cases kc
             JOIN users u ON kc.user_id = u.id
             WHERE {$where}
             ORDER BY kc.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        $this->render('admin/kyc/index', [
            'pageTitle' => 'KYC Review',
            'cases' => $cases,
            'page' => $page,
            'totalPages' => $totalPages,
            'statusFilter' => $status,
        ]);
    }

    public function show(string $id): void
    {
        $kycCase = $this->db->fetchOne(
            "SELECT kc.*, u.first_name, u.last_name, u.email, u.phone
             FROM kyc_cases kc JOIN users u ON kc.user_id = u.id
             WHERE kc.id = ?",
            [$id]
        );

        if (!$kycCase) {
            Flash::error('KYC case not found.');
            $this->redirect(Url::to('admin/kyc'));
            return;
        }

        $profile = $this->db->fetchOne("SELECT * FROM user_profiles WHERE user_id = ?", [$kycCase['user_id']]);

        $this->render('admin/kyc/show', [
            'pageTitle' => 'KYC Review',
            'case' => $kycCase,
            'profile' => $profile,
        ]);
    }

    public function review(string $id): void
    {
        if (!Csrf::validate($this->post('csrf_token'))) {
            Flash::error('Invalid request.');
            $this->redirectBack();
            return;
        }

        $newStatus = $this->post('status');
        $notes = trim($this->post('notes', ''));

        $allowedStatuses = ['approved', 'rejected', 'requires_review', 'pending'];
        if (!in_array($newStatus, $allowedStatuses)) {
            Flash::error('Invalid status.');
            $this->redirectBack();
            return;
        }

        $kycCase = $this->db->fetchOne("SELECT * FROM kyc_cases WHERE id = ?", [$id]);
        if (!$kycCase) {
            Flash::error('KYC case not found.');
            $this->redirect(Url::to('admin/kyc'));
            return;
        }

        $oldStatus = $kycCase['status'];

        $this->db->query(
            "UPDATE kyc_cases SET status = ?, reviewer_id = ?, reviewer_notes = ?, reviewed_at = NOW() WHERE id = ?",
            [$newStatus, $this->auth->userId(), $notes, $id]
        );

        $this->audit('kyc_reviewed', 'kyc_case', (int)$id,
            ['status' => $oldStatus], ['status' => $newStatus],
            "KYC review: {$oldStatus} -> {$newStatus}");

        // Notify the user
        $statusLabels = [
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'requires_review' => 'Requires Additional Review',
            'pending' => 'Under Review',
        ];

        $this->notify($kycCase['user_id'], 'kyc_reviewed', 'KYC Status Update',
            "Your identity verification has been updated to: {$statusLabels[$newStatus]}. " .
            ($notes ? "Notes: {$notes}" : ''),
            'kyc_case', (int)$id);

        Flash::success("KYC case updated to: {$statusLabels[$newStatus]}");
        $this->redirect(Url::to("admin/kyc/{$id}"));
    }
}
