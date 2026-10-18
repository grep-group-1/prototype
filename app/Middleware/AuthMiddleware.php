<?php
namespace App\Middleware;

use App\Services\Auth;

/**
 * Authentication Middleware
 * 
 * Redirects unauthenticated users to the login page.
 */
class AuthMiddleware
{
    public function handle(): void
    {
        $auth = Auth::getInstance();
        if (!$auth->check()) {
            \App\Helpers\Flash::warning('Please log in to continue.');
            header('Location: ' . \App\Helpers\Url::to('login'));
            exit;
        }
    }
}
