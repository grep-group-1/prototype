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
                    <h2>Welcome Back</h2>
                    <p class="subtitle">Log in to access your funding dashboard.</p>

                    <form method="POST" action="<?= Url::to('login') ?>">
                        <?= Csrf::field() ?>
                        <div class="mb-3">
                            <label for="email" class="ce-form-label">Email Address</label>
                            <input type="email" class="form-control ce-form-control" id="email" name="email" 
                                   placeholder="your@email.com" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="ce-form-label">Password</label>
                            <input type="password" class="form-control ce-form-control" id="password" name="password" 
                                   placeholder="Enter your password" required>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label fs-sm" for="remember">Remember me</label>
                            </div>
                            <a href="<?= Url::to('forgot-password') ?>" class="fs-sm">Forgot password?</a>
                        </div>
                        <button type="submit" class="btn btn-ce-primary w-100 mb-3">Log In</button>
                    </form>

                    <p class="text-center fs-sm text-muted-ce mb-0">
                        Don't have an account? <a href="<?= Url::to('register') ?>">Create one</a>
                    </p>

                    <hr class="my-3">
                    <div class="fs-sm text-muted-ce">
                        <p class="mb-1 fw-600">Demo Accounts:</p>
                        <p class="mb-1"><strong>Funder:</strong> funder@capitaledge.test / Funder@123</p>
                        <p class="mb-1"><strong>Admin:</strong> admin@capitaledge.test / Admin@123</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
