<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Helpers\{Url, Flash, Csrf};

/**
 * Admin Product Management Controller
 */
class ProductController extends BaseController
{
    public function index(): void
    {
        $products = $this->db->fetchAll(
            "SELECT fp.*, 
                    (SELECT COUNT(*) FROM agreement_acceptances WHERE product_id = fp.id AND is_active = 1) as active_acceptances
             FROM funding_products fp
             ORDER BY fp.name"
        );

        $this->render('admin/products/index', [
            'pageTitle' => 'Funding Products',
            'products' => $products,
        ]);
    }

    public function create(): void
    {
        $this->render('admin/products/create', [
            'pageTitle' => 'Create Funding Product',
        ]);
    }

    public function store(): void
    {
        if (!Csrf::validate($this->post('csrf_token'))) {
            Flash::error('Invalid request.');
            $this->redirect(Url::to('admin/products/create'));
            return;
        }

        $data = [
            'code' => strtoupper(trim($this->post('code', ''))),
            'name' => trim($this->post('name', '')),
            'description' => trim($this->post('description', '')),
            'product_type' => $this->post('product_type', ''),
            'profit_share_pct' => $this->post('profit_share_pct', '0'),
            'distribution_period' => $this->post('distribution_period') ?: null,
            'management_fee_pct' => $this->post('management_fee_pct', '0'),
            'entry_fee_pct' => $this->post('entry_fee_pct', '0'),
            'min_lock_in_days' => (int)$this->post('min_lock_in_days', 0),
            'withdrawal_notice_days' => (int)$this->post('withdrawal_notice_days', 30),
            'loss_carry_forward' => $this->post('loss_carry_forward') ? 1 : 0,
        ];

        // Validation
        if (empty($data['code']) || empty($data['name'])) {
            Flash::error('Code and name are required.');
            $this->redirect(Url::to('admin/products/create'));
            return;
        }

        // Check unique code
        $existing = $this->db->fetchOne("SELECT id FROM funding_products WHERE code = ?", [$data['code']]);
        if ($existing) {
            Flash::error('A product with this code already exists.');
            $this->redirect(Url::to('admin/products/create'));
            return;
        }

        $this->db->query(
            "INSERT INTO funding_products 
                (code, name, description, product_type, profit_share_pct, distribution_period,
                 management_fee_pct, entry_fee_pct, min_lock_in_days, withdrawal_notice_days, loss_carry_forward, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')",
            array_values($data)
        );

        $this->audit('product_created', 'funding_product', (int)$this->db->lastInsertId(), null, $data);
        Flash::success('Funding product created successfully.');
        $this->redirect(Url::to('admin/products'));
    }

    public function edit(string $id): void
    {
        $product = $this->db->fetchOne("SELECT * FROM funding_products WHERE id = ?", [$id]);
        if (!$product) {
            Flash::error('Product not found.');
            $this->redirect(Url::to('admin/products'));
            return;
        }

        $this->render('admin/products/edit', [
            'pageTitle' => 'Edit Funding Product',
            'product' => $product,
        ]);
    }

    public function update(string $id): void
    {
        if (!Csrf::validate($this->post('csrf_token'))) {
            Flash::error('Invalid request.');
            $this->redirect(Url::to("admin/products/{$id}/edit"));
            return;
        }

        $product = $this->db->fetchOne("SELECT * FROM funding_products WHERE id = ?", [$id]);
        if (!$product) {
            Flash::error('Product not found.');
            $this->redirect(Url::to('admin/products'));
            return;
        }

        $oldData = $product;
        
        $this->db->query(
            "UPDATE funding_products SET
                name = ?, description = ?, profit_share_pct = ?,
                management_fee_pct = ?, entry_fee_pct = ?,
                min_lock_in_days = ?, withdrawal_notice_days = ?,
                loss_carry_forward = ?, status = ?
             WHERE id = ?",
            [
                trim($this->post('name', '')),
                trim($this->post('description', '')),
                $this->post('profit_share_pct', '0'),
                $this->post('management_fee_pct', '0'),
                $this->post('entry_fee_pct', '0'),
                (int)$this->post('min_lock_in_days', 0),
                (int)$this->post('withdrawal_notice_days', 30),
                $this->post('loss_carry_forward') ? 1 : 0,
                $this->post('status', 'active'),
                $id,
            ]
        );

        $this->audit('product_updated', 'funding_product', (int)$id, $oldData, []);
        Flash::success('Product updated successfully.');
        $this->redirect(Url::to('admin/products'));
    }
}
