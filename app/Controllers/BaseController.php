<?php
namespace App\Controllers;

use App\Services\Auth;
use App\Services\Database;
use App\Helpers\{Url, Escape, Flash, Csrf, Format};

/**
 * Base Controller
 * 
 * Provides common functionality shared by all controllers:
 * rendering views, accessing auth/database, validation helpers.
 */
abstract class BaseController
{
    protected Auth $auth;
    protected Database $db;

    public function __construct()
    {
        $this->auth = Auth::getInstance();
        $this->db = Database::getInstance();
    }

    /**
     * Render a view with layout.
     * 
     * @param string $view View path relative to resources/views/ (e.g., 'auth/login')
     * @param array $data Variables to pass to the view
     * @param string|null $layout Layout to use (default: 'layouts/app')
     */
    protected function render(string $view, array $data = [], ?string $layout = 'layouts/app'): void
    {
        // Make common variables available to all views
        $data['auth'] = $this->auth;
        $data['currentUser'] = $this->auth->user();
        $data['flashMessages'] = Flash::getMessages();
        $data['pageTitle'] = $data['pageTitle'] ?? '';
        
        // Extract data so views can use variables directly
        extract($data);

        // Start output buffering for the view content
        ob_start();
        $viewFile = APP_BASE_PATH . '/resources/views/' . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewFile)) {
            ob_end_clean();
            throw new \RuntimeException("View not found: {$view} (looked in {$viewFile})");
        }
        
        require $viewFile;
        $content = ob_get_clean();

        // If a layout is specified, wrap the content in it
        if ($layout) {
            $layoutFile = APP_BASE_PATH . '/resources/views/' . $layout . '.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }

    /**
     * Render a view without a layout (for partials/AJAX).
     */
    protected function renderPartial(string $view, array $data = []): void
    {
        $this->render($view, $data, null);
    }

    /**
     * Redirect to a URL.
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Redirect to a named route.
     */
    protected function redirectTo(string $route, array $params = []): void
    {
        $this->redirect(Url::to($route, $params));
    }

    /**
     * Redirect back to the previous page.
     */
    protected function redirectBack(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? Url::base();
        $this->redirect($referer);
    }

    /**
     * Get a POST parameter.
     */
    protected function post(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get a GET parameter.
     */
    protected function get(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Get all POST data.
     */
    protected function allPost(): array
    {
        return $_POST;
    }

    /**
     * Return a JSON response.
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Validate required fields are present.
     */
    protected function validateRequired(array $fields, array $data): array
    {
        $errors = [];
        foreach ($fields as $field) {
            if (empty($data[$field]) && (!isset($data[$field]) || trim($data[$field]) === '')) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
            }
        }
        return $errors;
    }

    /**
     * Validate an email address.
     */
    protected function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate password strength.
     */
    protected function validatePassword(string $password): array
    {
        $errors = [];
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number.';
        }
        return $errors;
    }

    /**
     * Generate a unique reference string.
     */
    protected function generateReference(string $prefix = 'TXN'): string
    {
        return $prefix . '-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
    }

    /**
     * Log an audit trail entry.
     */
    protected function audit(string $action, ?string $entityType = null, ?int $entityId = null, 
                              ?array $oldValues = null, ?array $newValues = null, ?string $description = null): void
    {
        $this->db->query(
            "INSERT INTO audit_logs (user_id, action, entity_type, entity_id, old_values, new_values, ip_address, user_agent, description)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $this->auth->userId(),
                $action,
                $entityType,
                $entityId,
                $oldValues ? json_encode($oldValues) : null,
                $newValues ? json_encode($newValues) : null,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null,
                $description,
            ]
        );
    }

    /**
     * Create a notification for a user.
     */
    protected function notify(int $userId, string $type, string $title, string $message,
                               ?string $relatedType = null, ?int $relatedId = null): void
    {
        $this->db->query(
            "INSERT INTO notifications (user_id, type, title, message, related_type, related_id)
             VALUES (?, ?, ?, ?, ?, ?)",
            [$userId, $type, $title, $message, $relatedType, $relatedId]
        );
    }
}
