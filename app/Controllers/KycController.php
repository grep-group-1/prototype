<?php
namespace App\Controllers;

use App\Helpers\{Url, Flash, Csrf};

/**
 * KYC Controller (Funder side)
 * 
 * Handles the simulated KYC verification process for funders.
 * Funder can submit their information; compliance reviewers process it separately.
 */
class KycController extends BaseController
{
    public function show(): void
    {
        $userId = $this->auth->userId();

        // Get latest KYC case
        $kycCase = $this->db->fetchOne(
            "SELECT * FROM kyc_cases WHERE user_id = ? ORDER BY created_at DESC LIMIT 1",
            [$userId]
        );

        // Get user profile for pre-filling
        $profile = $this->db->fetchOne(
            "SELECT * FROM user_profiles WHERE user_id = ?",
            [$userId]
        );

        $this->render('funder/kyc', [
            'pageTitle' => 'Identity Verification',
            'kycCase' => $kycCase,
            'profile' => $profile,
        ]);
    }

    public function submit(): void
    {
        if (!Csrf::validate($this->post('csrf_token'))) {
            Flash::error('Invalid request.');
            $this->redirect(Url::to('kyc'));
            return;
        }

        $userId = $this->auth->userId();

        // Check if there's already a pending or approved case
        $existing = $this->db->fetchOne(
            "SELECT id, status FROM kyc_cases WHERE user_id = ? AND status IN ('pending', 'approved')",
            [$userId]
        );

        if ($existing) {
            if ($existing['status'] === 'approved') {
                Flash::warning('Your identity has already been verified.');
            } else {
                Flash::warning('A verification request is already pending review.');
            }
            $this->redirect(Url::to('kyc'));
            return;
        }

        // Update profile data from KYC form
        $dob = $this->post('date_of_birth') ?: null;
        $nationality = trim($this->post('nationality', ''));
        $address = trim($this->post('address', ''));
        $city = trim($this->post('city', ''));
        $country = trim($this->post('country', ''));
        $occupation = trim($this->post('occupation', ''));
        $sourceOfFunds = trim($this->post('source_of_funds', ''));

        // Simulated KYC result - randomly assign for demo purposes
        // In production, this would call a real verification provider
        $simulationResults = ['approved', 'pending', 'requires_review'];
        $simulationResult = $simulationResults[array_rand($simulationResults)];

        $verificationRef = 'KYC-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));

        $statusMap = [
            'approved' => 'approved',
            'pending' => 'pending',
            'requires_review' => 'requires_review',
        ];

        $this->db->beginTransaction();
        try {
            // Update profile
            $this->db->query(
                "UPDATE user_profiles SET 
                    date_of_birth = ?, nationality = ?, address = ?,
                    city = ?, country = ?, occupation = ?, source_of_funds = ?
                 WHERE user_id = ?",
                [$dob, $nationality ?: null, $address ?: null,
                 $city ?: null, $country ?: null, $occupation ?: null,
                 $sourceOfFunds ?: null, $userId]
            );

            // Create KYC case
            $this->db->query(
                "INSERT INTO kyc_cases (user_id, status, verification_reference, simulation_result, submitted_at)
                 VALUES (?, ?, ?, ?, NOW())",
                [$userId, $statusMap[$simulationResult], $verificationRef, $simulationResult]
            );

            $this->db->commit();

            $this->audit('kyc_submitted', 'kyc_case', (int)$this->db->lastInsertId(),
                null, ['status' => $statusMap[$simulationResult]]);

            $this->notify($userId, 'kyc_submitted', 'KYC Verification Submitted',
                'Your identity verification request has been submitted and is being processed. ' .
                'This is a simulated verification for testing purposes.');

            if ($simulationResult === 'approved') {
                Flash::success('Identity verification completed successfully! (Simulated)');
            } elseif ($simulationResult === 'requires_review') {
                Flash::info('Your verification requires additional review. A compliance officer will review your submission. (Simulated)');
            } else {
                Flash::info('Your verification is being processed. Please check back later. (Simulated)');
            }

        } catch (\Exception $e) {
            $this->db->rollback();
            Flash::error('Failed to submit verification. Please try again.');
        }

        $this->redirect(Url::to('kyc'));
    }
}
