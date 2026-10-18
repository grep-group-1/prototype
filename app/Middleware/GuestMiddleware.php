<?php
namespace App\Middleware;

use App\Services\Auth;

/**
 * Guest Middleware
 * 
 * Ensures the current user is NOT authenticated (for login/register pages).
 */
class GuestMiddleware
{
    public function handle(): void
    {
        $auth = Auth::getInstance();
        if ($auth->check()) {
            header('Location: ' . \App\Helpers\Url::to('dashboard'));
            exit;
        }
    }
}
