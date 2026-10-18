<section class="ce-section"><div class="container">
    <div class="text-center mb-5"><h1 class="ce-section-title">Frequently Asked Questions</h1></div>
    <div class="row justify-content-center"><div class="col-lg-8">
        <div class="accordion" id="faqPage">
            <?php $faqs = [
                ['What is this platform?', 'This is a university prototype demonstrating a simulated funding arrangement platform. No real money or financial services are involved.'],
                ['Are profits guaranteed?', 'No. Profits are NEVER guaranteed. Capital may be at risk. All figures shown are simulated test data.'],
                ['Is this a licensed investment service?', 'No. This is a prototype and does not constitute a licensed investment service, savings account, or financial product.'],
                ['How do I create an account?', 'Click "Get Started" or "Register" and fill in your details. Use the demo accounts for quick access.'],
                ['What is KYC?', 'KYC (Know Your Customer) is a simulated identity verification process. In this prototype, results are randomly generated for demonstration.'],
                ['How do deposits work?', 'Deposits are simulated through a test payment gateway. No real money moves. The system records the simulated deposit for demonstration.'],
                ['Can I withdraw my money?', 'You can submit simulated withdrawal requests. Since no real money is involved, all withdrawals are also simulated.'],
                ['What are the fees?', 'Fees are configurable contractual parameters for demonstration. In Option A: 2% management fee, 0.5% entry fee. In Option B: 2.5% management fee, 0.5% entry fee.'],
                ['Is my data safe?', 'This is a local development environment running on XAMPP. Do not enter real personal information. Passwords are hashed with bcrypt.'],
                ['Can I use this for real business?', 'No. Before any real-money deployment, this would require legal review, regulatory approval, security audit, and proper licensing.'],
            ]; ?>
            <?php foreach ($faqs as $i => $faq): ?>
            <div class="accordion-item border-0 mb-3 ce-card">
                <h2 class="accordion-header"><button class="accordion-button collapsed fw-600" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>"><?= $faq[0] ?></button></h2>
                <div id="faq<?= $i ?>" class="accordion-collapse collapse" data-bs-parent="#faqPage"><div class="accordion-body text-muted-ce"><?= $faq[1] ?></div></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div></div>
</div></section>
