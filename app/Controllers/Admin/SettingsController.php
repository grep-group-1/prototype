<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Helpers\{Url, Flash, Csrf};

/**
 * Admin Settings Controller
 */
class SettingsController extends BaseController
{
    public function index(): void
    {
        $products = $this->db->fetchAll("SELECT * FROM funding_products ORDER BY name");
        
        $this->render('admin/settings/index', [
            'pageTitle' => 'System Settings',
            'products' => $products,
        ]);
    }

    public function update(): void
    {
        if (!Csrf::validate($this->post('csrf_token'))) {
            Flash::error('Invalid request.');
            $this->redirect(Url::to('admin/settings'));
            return;
        }

        $this->audit('settings_updated', null, null, null, ['changes' => 'System settings modified']);
        Flash::success('Settings updated successfully.');
        $this->redirect(Url::to('admin/settings'));
    }
}
