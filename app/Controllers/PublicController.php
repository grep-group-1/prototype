<?php
namespace App\Controllers;

use App\Helpers\Url;

/**
 * Public Controller
 * 
 * Handles all public-facing pages that don't require authentication.
 */
class PublicController extends BaseController
{
    public function home(): void
    {
        $this->render('public/home', [
            'pageTitle' => 'Premium Funding Platform',
        ], 'layouts/public');
    }

    public function about(): void
    {
        $this->render('public/about', [
            'pageTitle' => 'About',
        ], 'layouts/public');
    }

    public function howItWorks(): void
    {
        $this->render('public/how-it-works', [
            'pageTitle' => 'How It Works',
        ], 'layouts/public');
    }

    public function terms(): void
    {
        $this->render('public/terms', [
            'pageTitle' => 'Terms of Service',
        ], 'layouts/public');
    }

    public function privacy(): void
    {
        $this->render('public/privacy', [
            'pageTitle' => 'Privacy Policy',
        ], 'layouts/public');
    }

    public function riskDisclosure(): void
    {
        $this->render('public/risk-disclosure', [
            'pageTitle' => 'Risk Disclosure',
        ], 'layouts/public');
    }

    public function faq(): void
    {
        $this->render('public/faq', [
            'pageTitle' => 'Frequently Asked Questions',
        ], 'layouts/public');
    }

    public function contact(): void
    {
        $this->render('public/contact', [
            'pageTitle' => 'Contact & Support',
        ], 'layouts/public');
    }
}
