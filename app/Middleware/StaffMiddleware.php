<?php
namespace App\Middleware;

use App\Services\Auth;

/**
 * Staff Middleware
 * 
 * Ensures the current user is a staff member (admin, compliance, or finance).
 */
class StaffMiddleware
{
    public function handle(): void
    {
        $auth = Auth::getInstance();
        if (!$auth->check()) {
            header('Location: ' . \App\Helpers\Url::to('login'));
            exit;
        }
        if (!$auth->hasAnyRole(['admin', 'compliance', 'finance'])) {
            http_response_code(403);
            require dirname(__DIR__, 2) . '/resources/views/errors/403.php';
            exit;
        }
    }
}
