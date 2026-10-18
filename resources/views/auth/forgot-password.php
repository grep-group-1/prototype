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
                    <h2>Forgot Password</h2>
                    <p class="subtitle">Enter your email address and we'll send a reset link.</p>
                    <form method="POST" action="<?= Url::to('forgot-password') ?>">
                        <?= Csrf::field() ?>
                        <div class="mb-3">
                            <label for="email" class="ce-form-label">Email Address</label>
                            <input type="email" class="form-control ce-form-control" id="email" name="email" required autofocus>
                        </div>
                        <button type="submit" class="btn btn-ce-primary w-100 mb-3">Send Reset Link</button>
                    </form>
                    <p class="text-center fs-sm text-muted-ce mb-0">
                        <a href="<?= Url::to('login') ?>">Back to login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
