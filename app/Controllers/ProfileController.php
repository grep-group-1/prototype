<?php
namespace App\Controllers;

use App\Helpers\{Url, Flash, Csrf};

/**
 * Profile Controller
 * 
 * Handles user profile management (view and update personal details).
 */
class ProfileController extends BaseController
{
    public function show(): void
    {
        $user = $this->auth->user();
        $profile = $this->db->fetchOne(
            "SELECT * FROM user_profiles WHERE user_id = ?",
            [$user['id']]
        );

        $this->render('funder/profile', [
            'pageTitle' => 'My Profile',
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    public function update(): void
    {
        if (!Csrf::validate($this->post('csrf_token'))) {
            Flash::error('Invalid request.');
            $this->redirect(Url::to('profile'));
            return;
        }

        $userId = $this->auth->userId();
        $phone = trim($this->post('phone', ''));
        $dob = $this->post('date_of_birth') ?: null;
        $nationality = trim($this->post('nationality', ''));
        $address = trim($this->post('address', ''));
        $city = trim($this->post('city', ''));
        $country = trim($this->post('country', ''));
        $postalCode = trim($this->post('postal_code', ''));
        $occupation = trim($this->post('occupation', ''));
        $sourceOfFunds = trim($this->post('source_of_funds', ''));

        $this->db->beginTransaction();
        try {
            // Update user table
            $this->db->query(
                "UPDATE users SET phone = ? WHERE id = ?",
                [$phone ?: null, $userId]
            );

            // Update profile table
            $this->db->query(
                "UPDATE user_profiles SET 
                    date_of_birth = ?, nationality = ?, address = ?,
                    city = ?, country = ?, postal_code = ?,
                    occupation = ?, source_of_funds = ?
                 WHERE user_id = ?",
                [$dob, $nationality ?: null, $address ?: null,
                 $city ?: null, $country ?: null, $postalCode ?: null,
                 $occupation ?: null, $sourceOfFunds ?: null, $userId]
            );

            $this->db->commit();
            $this->audit('profile_updated', 'user', $userId);
            Flash::success('Profile updated successfully.');
        } catch (\Exception $e) {
            $this->db->rollback();
            Flash::error('Failed to update profile. Please try again.');
        }

        $this->redirect(Url::to('profile'));
    }
}
