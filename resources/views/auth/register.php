<div class="ce-auth-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="text-center mb-4">
                    <a href="<?= Url::base() ?>" class="text-white text-decoration-none fs-3 fw-bold">
                        Capital<span style="color: var(--ce-gold)">Edge</span>
                    </a>
                </div>
                <div class="ce-auth-card">
                    <h2>Create Account</h2>
                    <p class="subtitle">Register to access the funding platform.</p>

                    <form method="POST" action="<?= Url::to('register') ?>">
                        <?= Csrf::field() ?>
                        <div class="row g-3">
                            <div class="col-6">
                                <label for="first_name" class="ce-form-label">First Name</label>
                                <input type="text" class="form-control ce-form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="col-6">
                                <label for="last_name" class="ce-form-label">Last Name</label>
                                <input type="text" class="form-control ce-form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="email" class="ce-form-label">Email Address</label>
                            <input type="email" class="form-control ce-form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="ce-form-label">Phone (optional)</label>
                            <input type="tel" class="form-control ce-form-control" id="phone" name="phone" placeholder="+256...">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="ce-form-label">Password</label>
                            <input type="password" class="form-control ce-form-control" id="password" name="password" required
                                   minlength="8">
                            <div class="form-text">Min 8 characters, with uppercase, lowercase, and number.</div>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="ce-form-label">Confirm Password</label>
                            <input type="password" class="form-control ce-form-control" id="password_confirmation" 
                                   name="password_confirmation" required>
                        </div>

                        <div class="ce-risk-box mb-3">
                            <h6><i class="bi bi-exclamation-triangle-fill me-1"></i> Risk Acknowledgment</h6>
                            <p>I understand that this is a simulated platform. In a production environment, 
                            capital would be at risk and profits are not guaranteed. I have read the 
                            <a href="<?= Url::to('risk-disclosure') ?>" target="_blank">Risk Disclosure</a>.</p>
                        </div>

                        <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" id="accept_terms" name="accept_terms" required>
                            <label class="form-check-label fs-sm" for="accept_terms">
                                I agree to the <a href="<?= Url::to('terms') ?>" target="_blank">Terms of Service</a>
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="accept_risk" name="accept_risk" required>
                            <label class="form-check-label fs-sm" for="accept_risk">
                                I acknowledge the <a href="<?= Url::to('risk-disclosure') ?>" target="_blank">Risk Disclosure</a>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-ce-primary w-100 mb-3">Create Account</button>
                    </form>

                    <p class="text-center fs-sm text-muted-ce mb-0">
                        Already have an account? <a href="<?= Url::to('login') ?>">Log in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
