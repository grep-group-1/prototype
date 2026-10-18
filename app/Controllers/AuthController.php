<?php
namespace App\Controllers;

use App\Helpers\{Url, Flash, Csrf};
use App\Services\Auth;

/**
 * Authentication Controller
 * 
 * Handles user registration, login, logout, password reset, and email verification.
 */
class AuthController extends BaseController
{
    /**
     * Show the login form.
     */
    public function showLogin(): void
    {
        $this->render('auth/login', [
            'pageTitle' => 'Log In',
        ], 'layouts/public');
    }

    /**
     * Process login form submission.
     */
    public function login(): void
    {
        if (!Csrf::validate($this->post('csrf_token'))) {
            Flash::error('Invalid request. Please try again.');
            $this->redirect(Url::to('login'));
            return;
        }

        $email = trim($this->post('email', ''));
        $password = $this->post('password', '');

        // Validate inputs
        if (empty($email) || empty($password)) {
            Flash::error('Please enter your email and password.');
            $this->redirect(Url::to('login'));
            return;
        }

        $result = $this->auth->attempt($email, $password);

        if ($result['success']) {
            $this->audit('user_login', 'user', $this->auth->userId());
            
            // Redirect based on role
            if ($this->auth->hasAnyRole(['admin', 'compliance', 'finance'])) {
                $this->redirect(Url::to('admin'));
            } else {
                $this->redirect(Url::to('dashboard'));
            }
        } else {
            Flash::error($result['message']);
            $this->redirect(Url::to('login'));
        }
    }

    /**
     * Show the registration form.
     */
    public function showRegister(): void
    {
        $this->render('auth/register', [
            'pageTitle' => 'Create Account',
        ], 'layouts/public');
    }

    /**
     * Process registration form submission.
     */
    public function register(): void
    {
        if (!Csrf::validate($this->post('csrf_token'))) {
            Flash::error('Invalid request. Please try again.');
            $this->redirect(Url::to('register'));
            return;
        }

        $firstName = trim($this->post('first_name', ''));
        $lastName = trim($this->post('last_name', ''));
        $email = trim($this->post('email', ''));
        $phone = trim($this->post('phone', ''));
        $password = $this->post('password', '');
        $passwordConfirm = $this->post('password_confirmation', '');
        $acceptTerms = $this->post('accept_terms');
        $acceptRisk = $this->post('accept_risk');

        // Validation
        $errors = [];
        
        if (empty($firstName)) {
            $errors[] = 'First name is required.';
        }
        if (empty($lastName)) {
            $errors[] = 'Last name is required.';
        }
        if (empty($email) || !$this->validateEmail($email)) {
            $errors[] = 'A valid email address is required.';
        }
        if (empty($password)) {
            $errors[] = 'Password is required.';
        } else {
            $passwordErrors = $this->validatePassword($password);
            $errors = array_merge($errors, $passwordErrors);
        }
        if ($password !== $passwordConfirm) {
            $errors[] = 'Passwords do not match.';
        }
        if (!$acceptTerms) {
            $errors[] = 'You must accept the Terms of Service.';
        }
        if (!$acceptRisk) {
            $errors[] = 'You must acknowledge the Risk Disclosure.';
        }

        // Check if email already exists
        if (empty($errors)) {
            $existing = $this->db->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
            if ($existing) {
                $errors[] = 'An account with this email address already exists.';
            }
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                Flash::error($error);
            }
            $this->redirect(Url::to('register'));
            return;
        }

        // Create the user
        try {
            $this->db->beginTransaction();

            $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

            $this->db->query(
                "INSERT INTO users (email, password, first_name, last_name, phone, status, email_verified_at)
                 VALUES (?, ?, ?, ?, ?, 'active', NOW())",
                [$email, $passwordHash, $firstName, $lastName, $phone ?: null]
            );

            $userId = (int) $this->db->lastInsertId();

            // Assign funder role
            $role = $this->db->fetchOne("SELECT id FROM roles WHERE name = 'funder'");
            if ($role) {
                $this->db->query(
                    "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)",
                    [$userId, $role['id']]
                );
            }

            // Create user profile
            $this->db->query(
                "INSERT INTO user_profiles (user_id) VALUES (?)",
                [$userId]
            );

            // Create financial accounts for the user
            $this->db->query(
                "INSERT INTO financial_accounts (user_id, account_code, account_name, account_type, currency)
                 VALUES (?, ?, ?, 'liability', 'UGX')",
                [$userId, "USR{$userId}_DEPOSIT", "{$firstName} Deposit Wallet"]
            );
            $this->db->query(
                "INSERT INTO financial_accounts (user_id, account_code, account_name, account_type, currency)
                 VALUES (?, ?, ?, 'asset', 'UGX')",
                [$userId, "USR{$userId}_CAPITAL", "{$firstName} Allocated Capital"]
            );
            $this->db->query(
                "INSERT INTO financial_accounts (user_id, account_code, account_name, account_type, currency)
                 VALUES (?, ?, ?, 'liability', 'UGX')",
                [$userId, "USR{$userId}_DISTRIBUTION", "{$firstName} Distribution Balance"]
            );
            $this->db->query(
                "INSERT INTO financial_accounts (user_id, account_code, account_name, account_type, currency)
                 VALUES (?, ?, ?, 'liability', 'UGX')",
                [$userId, "USR{$userId}_WITHDRAWABLE", "{$firstName} Withdrawable Balance"]
            );

            $this->db->commit();

            // Log the registration
            $this->audit('user_registered', 'user', $userId, null, ['email' => $email]);

            // Create welcome notification
            $this->notify(
                $userId,
                'welcome',
                'Welcome to CapitalEdge Funding',
                'Your account has been created successfully. Please complete your KYC verification to get started.',
            );

            Flash::success('Account created successfully! Please log in.');
            $this->redirect(Url::to('login'));

        } catch (\Exception $e) {
            $this->db->rollback();
            Flash::error('Registration failed. Please try again.');
            $this->redirect(Url::to('register'));
        }
    }

    /**
     * Log out the current user.
     */
    public function logout(): void
    {
        if ($this->auth->check()) {
            $this->audit('user_logout', 'user', $this->auth->userId());
        }
        $this->auth->logout();
        Flash::success('You have been logged out.');
        $this->redirect(Url::base());
    }

    /**
     * Show the forgot password form.
     */
    public function showForgotPassword(): void
    {
        $this->render('auth/forgot-password', [
            'pageTitle' => 'Forgot Password',
        ], 'layouts/public');
    }

    /**
     * Process forgot password form.
     */
    public function forgotPassword(): void
    {
        if (!Csrf::validate($this->post('csrf_token'))) {
            Flash::error('Invalid request.');
            $this->redirect(Url::to('forgot-password'));
            return;
        }

        $email = trim($this->post('email', ''));
        
        if (empty($email) || !$this->validateEmail($email)) {
            Flash::error('Please enter a valid email address.');
            $this->redirect(Url::to('forgot-password'));
            return;
        }

        // Find user
        $user = $this->db->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);

        if ($user) {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour

            $this->db->query(
                "INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)",
                [$user['id'], $token, $expiresAt]
            );

            // In a real application, this would send an email.
            // For local testing, we'll flash the link.
            $resetUrl = Url::to("reset-password/{$token}");
            Flash::info("TEST MODE: Password reset link: {$resetUrl}");
            
            $this->audit('password_reset_requested', 'user', $user['id']);
        }

        // Always show success (don't reveal if email exists)
        Flash::success('If an account exists with that email, a password reset link has been sent.');
        $this->redirect(Url::to('login'));
    }

    /**
     * Show the reset password form.
     */
    public function showResetPassword(string $token): void
    {
        // Validate token
        $resetToken = $this->db->fetchOne(
            "SELECT * FROM password_reset_tokens 
             WHERE token = ? AND expires_at > NOW() AND used_at IS NULL",
            [$token]
        );

        if (!$resetToken) {
            Flash::error('This password reset link is invalid or has expired.');
            $this->redirect(Url::to('login'));
            return;
        }

        $this->render('auth/reset-password', [
            'pageTitle' => 'Reset Password',
            'token' => Escape::html($token),
        ], 'layouts/public');
    }

    /**
     * Process password reset.
     */
    public function resetPassword(string $token): void
    {
        if (!Csrf::validate($this->post('csrf_token'))) {
            Flash::error('Invalid request.');
            $this->redirect(Url::to('login'));
            return;
        }

        $password = $this->post('password', '');
        $passwordConfirm = $this->post('password_confirmation', '');

        // Validate token
        $resetToken = $this->db->fetchOne(
            "SELECT * FROM password_reset_tokens 
             WHERE token = ? AND expires_at > NOW() AND used_at IS NULL",
            [$token]
        );

        if (!$resetToken) {
            Flash::error('This password reset link is invalid or has expired.');
            $this->redirect(Url::to('login'));
            return;
        }

        // Validate password
        $passwordErrors = $this->validatePassword($password);
        if (!empty($passwordErrors)) {
            foreach ($passwordErrors as $error) {
                Flash::error($error);
            }
            $this->redirect(Url::to("reset-password/{$token}"));
            return;
        }

        if ($password !== $passwordConfirm) {
            Flash::error('Passwords do not match.');
            $this->redirect(Url::to("reset-password/{$token}"));
            return;
        }

        // Update password
        $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
        
        $this->db->beginTransaction();
        try {
            $this->db->query(
                "UPDATE users SET password = ? WHERE id = ?",
                [$newHash, $resetToken['user_id']]
            );

            $this->db->query(
                "UPDATE password_reset_tokens SET used_at = NOW() WHERE id = ?",
                [$resetToken['id']]
            );

            $this->db->commit();
            $this->audit('password_reset_completed', 'user', $resetToken['user_id']);
            
            Flash::success('Password has been reset. Please log in with your new password.');
            $this->redirect(Url::to('login'));

        } catch (\Exception $e) {
            $this->db->rollback();
            Flash::error('Password reset failed. Please try again.');
            $this->redirect(Url::to("reset-password/{$token}"));
        }
    }

    /**
     * Verify email address via token.
     */
    public function verifyEmail(string $token): void
    {
        $verifyToken = $this->db->fetchOne(
            "SELECT * FROM email_verification_tokens 
             WHERE token = ? AND expires_at > NOW() AND verified_at IS NULL",
            [$token]
        );

        if (!$verifyToken) {
            Flash::error('Invalid or expired verification link.');
            $this->redirect(Url::to('login'));
            return;
        }

        $this->db->beginTransaction();
        try {
            $this->db->query(
                "UPDATE users SET email_verified_at = NOW() WHERE id = ?",
                [$verifyToken['user_id']]
            );
            $this->db->query(
                "UPDATE email_verification_tokens SET verified_at = NOW() WHERE id = ?",
                [$verifyToken['id']]
            );
            $this->db->commit();

            Flash::success('Email verified successfully!');
        } catch (\Exception $e) {
            $this->db->rollback();
            Flash::error('Verification failed. Please try again.');
        }

        $this->redirect(Url::to('login'));
    }
}
