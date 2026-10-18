<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Escape::html($pageTitle ?? 'CapitalEdge Funding') ?> | CapitalEdge Funding</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= Url::asset('css/style.css') ?>" rel="stylesheet">
</head>
<body>

<!-- Public Navigation -->
<nav class="navbar navbar-expand-lg ce-navbar">
    <div class="container">
        <a class="navbar-brand" href="<?= Url::base() ?>">
            Capital<span class="brand-accent">Edge</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav">
            <i class="bi bi-list text-white fs-4"></i>
        </button>
        <div class="collapse navbar-collapse" id="publicNav">
            <ul class="navbar-nav me-auto ms-lg-4">
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('about') ?>">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('how-it-works') ?>">How It Works</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('faq') ?>">FAQ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to('contact') ?>">Contact</a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <?php if ($auth->check()): ?>
                    <a href="<?= Url::to('dashboard') ?>" class="btn btn-sm btn-nav-cta">Dashboard</a>
                <?php else: ?>
                    <a href="<?= Url::to('login') ?>" class="nav-link">Log In</a>
                    <a href="<?= Url::to('register') ?>" class="btn btn-sm btn-nav-cta">Get Started</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Flash Messages -->
<?php if (!empty($flashMessages)): ?>
<div class="container mt-3">
    <?php foreach ($flashMessages as $msg): ?>
        <div class="alert alert-<?= Escape::html($msg['type']) ?> alert-dismissible fade show ce-alert ce-alert-<?= Escape::html($msg['type']) ?>" role="alert">
            <?= Escape::html($msg['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Page Content -->
<?= $content ?>

<!-- Footer -->
<footer class="ce-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <h5 class="text-white fw-bold mb-3">Capital<span style="color: var(--ce-gold)">Edge</span></h5>
                <p class="fs-sm" style="color: rgba(255,255,255,0.5); max-width: 300px;">
                    A simulated funding platform for educational and demonstration purposes. 
                    No real money or investment services are involved.
                </p>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Platform</h6>
                <a href="<?= Url::to('about') ?>">About</a>
                <a href="<?= Url::to('how-it-works') ?>">How It Works</a>
                <a href="<?= Url::to('faq') ?>">FAQ</a>
                <a href="<?= Url::to('contact') ?>">Contact</a>
            </div>
            <div class="col-6 col-lg-3">
                <h6>Legal</h6>
                <a href="<?= Url::to('terms') ?>">Terms of Service</a>
                <a href="<?= Url::to('privacy') ?>">Privacy Policy</a>
                <a href="<?= Url::to('risk-disclosure') ?>">Risk Disclosure</a>
            </div>
            <div class="col-lg-3">
                <h6>Demo Access</h6>
                <p class="fs-sm mb-1" style="color: rgba(255,255,255,0.5);">
                    <i class="bi bi-person-circle me-1"></i> funder@capitaledge.test
                </p>
                <p class="fs-sm mb-1" style="color: rgba(255,255,255,0.5);">
                    <i class="bi bi-shield-lock me-1"></i> admin@capitaledge.test
                </p>
                <p class="fs-sm mt-2" style="color: rgba(255,255,255,0.35);">
                    This is a TEST environment. No real funds.
                </p>
            </div>
        </div>
        <div class="footer-bottom text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> CapitalEdge Funding &mdash; University Prototype. All data is simulated for testing purposes only.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= Url::asset('js/app.js') ?>"></script>
</body>
</html>
