<!-- Hero Section -->
<section class="ce-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1>Structured Funding<br>Arrangements for<br>Qualified Participants</h1>
                <p class="lead">
                    CapitalEdge provides a transparent, contractually-defined funding arrangement 
                    where participants contribute capital under agreed terms and share in eligible 
                    realized results.
                </p>
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="<?= Url::to('register') ?>" class="btn btn-hero">Get Started</a>
                    <a href="<?= Url::to('how-it-works') ?>" class="btn btn-hero-outline">How It Works</a>
                </div>
                <div class="ce-risk-box mt-4" style="background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.15);">
                    <h6 style="color: var(--ce-gold);"><i class="bi bi-exclamation-triangle-fill me-1"></i> Risk Disclosure</h6>
                    <p style="color: rgba(255,255,255,0.7);">
                        Capital is at risk. Profits are not guaranteed. This platform operates in test/demo mode 
                        and does not involve real money or constitute a licensed investment service.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Preview -->
<section class="ce-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="ce-section-title">How It Works</h2>
            <p class="ce-section-subtitle mx-auto">A clear, structured process from registration to funding arrangement.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="ce-card h-100">
                    <div class="ce-card-body text-center py-4">
                        <div class="ce-stat-card stat-icon bg-navy mx-auto mb-3" style="width:56px;height:56px;">
                            <i class="bi bi-person-plus fs-4"></i>
                        </div>
                        <h5>Register & Verify</h5>
                        <p class="text-muted-ce fs-sm">Create your account and complete simulated identity verification through our KYC process.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="ce-card h-100">
                    <div class="ce-card-body text-center py-4">
                        <div class="ce-stat-card stat-icon bg-gold mx-auto mb-3" style="width:56px;height:56px;">
                            <i class="bi bi-file-earmark-check fs-4"></i>
                        </div>
                        <h5>Choose Your Arrangement</h5>
                        <p class="text-muted-ce fs-sm">Review contractual terms and select the funding arrangement that suits your objectives.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="ce-card h-100">
                    <div class="ce-card-body text-center py-4">
                        <div class="ce-stat-card stat-icon bg-success mx-auto mb-3" style="width:56px;height:56px;">
                            <i class="bi bi-graph-up-arrow fs-4"></i>
                        </div>
                        <h5>Track & Report</h5>
                        <p class="text-muted-ce fs-sm">Monitor your capital allocation, view financial results, and download account statements.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="<?= Url::to('how-it-works') ?>" class="btn-ce-outline">Learn More <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    </div>
</section>

<!-- Funding Arrangements Overview -->
<section class="ce-section ce-section-alt">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="ce-section-title">Funding Arrangement Options</h2>
            <p class="ce-section-subtitle mx-auto">Two configurable contractual structures designed for transparency.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="ce-card h-100">
                    <div class="ce-card-body">
                        <span class="ce-badge ce-badge-navy mb-3">Option A</span>
                        <h4 class="mt-2">Profit Sharing</h4>
                        <p class="text-muted-ce fs-sm">
                            Receive an agreed share of eligible realized profits. Distributions are calculated 
                            when financial results are finalized and approved. Flexible with no fixed schedule.
                        </p>
                        <ul class="list-unstyled fs-sm mt-3">
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>60% profit share (illustrative)</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>No fixed distribution schedule</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Losses carry forward</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>90-day minimum lock-in</li>
                        </ul>
                        <a href="<?= Url::to('how-it-works') ?>" class="btn-ce-outline mt-2">Learn More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="ce-card h-100">
                    <div class="ce-card-body">
                        <span class="ce-badge ce-badge-info mb-3">Option B</span>
                        <h4 class="mt-2">Periodic Distributions</h4>
                        <p class="text-muted-ce fs-sm">
                            Receive an agreed share of eligible realized profits on a defined distribution schedule. 
                            Monthly calculation based on results realized during each period.
                        </p>
                        <ul class="list-unstyled fs-sm mt-3">
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>55% profit share (illustrative)</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Monthly distribution schedule</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Zero distribution if no profit</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>60-day minimum lock-in</li>
                        </ul>
                        <a href="<?= Url::to('how-it-works') ?>" class="btn-ce-outline mt-2">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Preview -->
<section class="ce-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="ce-section-title">Frequently Asked Questions</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item border-0 mb-3 ce-card">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-600" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                What is this platform?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted-ce">
                                This is a university prototype demonstrating a simulated funding arrangement platform. 
                                It does not involve real money, investments, or financial services. It is designed 
                                for educational and demonstration purposes only.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 mb-3 ce-card">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-600" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Are profits guaranteed?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted-ce">
                                <strong>No.</strong> Profits are never guaranteed. Capital may be at risk. Any simulated 
                                results shown on this platform are test data and do not represent actual or projected returns.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 mb-3 ce-card">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-600" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Is this a licensed investment service?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted-ce">
                                <strong>No.</strong> This is a prototype and does not constitute a licensed investment service, 
                                savings account, or financial product. Before any real-money deployment, appropriate 
                                regulatory approvals and legal advice would be required.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="<?= Url::to('faq') ?>" class="btn-ce-outline">View All FAQs</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="ce-section ce-section-alt">
    <div class="container text-center">
        <h2 class="ce-section-title">Ready to Explore?</h2>
        <p class="ce-section-subtitle mx-auto mb-4">
            Create a test account to experience the full platform with simulated deposits, 
            agreement management, and financial reporting.
        </p>
        <a href="<?= Url::to('register') ?>" class="btn-ce-primary">Create Test Account</a>
    </div>
</section>
