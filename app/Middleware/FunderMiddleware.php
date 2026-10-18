<?php
namespace App\Middleware;

use App\Services\Auth;

/**
 * Funder Middleware
 * 
 * Ensures the current user has the funder role.
 */
class FunderMiddleware
{
    public function handle(): void
    {
        $auth = Auth::getInstance();
        if (!$auth->check()) {
            header('Location: ' . \App\Helpers\Url::to('login'));
            exit;
        }
        if (!$auth->isFunder()) {
            http_response_code(403);
            require dirname(__DIR__, 2) . '/resources/views/errors/403.php';
            exit;
        }
    }
}
