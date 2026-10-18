<?php
namespace App\Controllers;

use App\Helpers\{Url, Flash, Csrf};

/**
 * Agreement Controller
 * 
 * Handles viewing available funding arrangements and accepting agreements.
 */
class AgreementController extends BaseController
{
    public function index(): void
    {
        $userId = $this->auth->userId();

        // Get active funding products with their current agreements
        $products = $this->db->fetchAll(
            "SELECT fp.*, fa.id as agreement_id, fa.title as agreement_title, fa.body as agreement_body
             FROM funding_products fp
             JOIN funding_agreements fa ON fa.product_id = fp.id AND fa.is_current = 1
             WHERE fp.status = 'active'
             ORDER BY fp.name"
        );

        // Get user's accepted agreements
        $acceptedAgreements = $this->db->fetchAll(
            "SELECT aa.*, fp.name as product_name, fp.code as product_code
             FROM agreement_acceptances aa
             JOIN funding_products fp ON aa.product_id = fp.id
             WHERE aa.user_id = ? AND aa.is_active = 1",
            [$userId]
        );

        // Build a map of accepted product IDs
        $acceptedProductIds = array_column($acceptedAgreements, 'product_id');

        $this->render('funder/agreements', [
            'pageTitle' => 'Funding Arrangements',
            'products' => $products,
            'acceptedProductIds' => $acceptedProductIds,
        ]);
    }

    public function show(string $id): void
    {
        $userId = $this->auth->userId();

        $product = $this->db->fetchOne(
            "SELECT fp.*, fa.id as agreement_id, fa.title as agreement_title, 
                    fa.body as agreement_body, fa.version as agreement_version,
                    fa.effective_from, fa.profit_share_pct, fa.management_fee_pct,
                    fa.entry_fee_pct, fa.min_lock_in_days, fa.loss_carry_forward,
                    fa.distribution_period
             FROM funding_products fp
             JOIN funding_agreements fa ON fa.product_id = fp.id AND fa.is_current = 1
             WHERE fp.id = ? AND fp.status = 'active'",
            [$id]
        );

        if (!$product) {
            Flash::error('Funding arrangement not found.');
            $this->redirect(Url::to('agreements'));
            return;
        }

        // Check if user already accepted this product
        $existingAcceptance = $this->db->fetchOne(
            "SELECT * FROM agreement_acceptances 
             WHERE user_id = ? AND product_id = ? AND is_active = 1",
            [$userId, $id]
        );

        $this->render('funder/agreement-show', [
            'pageTitle' => $product['agreement_title'],
            'product' => $product,
            'existingAcceptance' => $existingAcceptance,
        ]);
    }

    public function accept(string $id): void
    {
        if (!Csrf::validate($this->post('csrf_token'))) {
            Flash::error('Invalid request.');
            $this->redirect(Url::to('agreements'));
            return;
        }

        $userId = $this->auth->userId();

        // Verify the product exists and is active
        $product = $this->db->fetchOne(
            "SELECT fp.*, fa.id as agreement_id, fa.version as agreement_version,
                    fa.profit_share_pct, fa.management_fee_pct, fa.entry_fee_pct,
                    fa.min_lock_in_days, fa.loss_carry_forward
             FROM funding_products fp
             JOIN funding_agreements fa ON fa.product_id = fp.id AND fa.is_current = 1
             WHERE fp.id = ? AND fp.status = 'active'",
            [$id]
        );

        if (!$product) {
            Flash::error('Funding arrangement not found or not active.');
            $this->redirect(Url::to('agreements'));
            return;
        }

        // Check if user already has an active acceptance for this product
        $existing = $this->db->fetchOne(
            "SELECT id FROM agreement_acceptances 
             WHERE user_id = ? AND product_id = ? AND is_active = 1",
            [$userId, $id]
        );

        if ($existing) {
            Flash::warning('You have already accepted this funding arrangement.');
            $this->redirect(Url::to('agreements'));
            return;
        }

        // Check that user has accepted risk disclosure
        $acceptedRisk = $this->post('accept_risk');
        if (!$acceptedRisk) {
            Flash::error('You must acknowledge the risk disclosure before accepting this agreement.');
            $this->redirect(Url::to("agreements/{$id}"));
            return;
        }

        $this->db->beginTransaction();
        try {
            $this->db->query(
                "INSERT INTO agreement_acceptances 
                    (user_id, agreement_id, product_id, profit_share_pct, management_fee_pct, 
                     entry_fee_pct, ip_address, user_agent)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $userId,
                    $product['agreement_id'],
                    $id,
                    $product['profit_share_pct'],
                    $product['management_fee_pct'],
                    $product['entry_fee_pct'],
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null,
                ]
            );

            $this->db->commit();

            $this->audit('agreement_accepted', 'agreement_acceptance', (int)$this->db->lastInsertId(),
                null, ['product_id' => (int)$id, 'agreement_id' => (int)$product['agreement_id']]);

            $this->notify($userId, 'agreement_accepted', 'Agreement Accepted',
                'You have accepted the ' . $product['name'] . ' funding arrangement.');

            Flash::success('Funding arrangement accepted successfully.');
        } catch (\Exception $e) {
            $this->db->rollback();
            Flash::error('Failed to accept agreement. Please try again.');
        }

        $this->redirect(Url::to('dashboard'));
    }
}
