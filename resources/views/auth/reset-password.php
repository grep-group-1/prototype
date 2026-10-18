<div class="ce-auth-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="ce-auth-card">
                    <h2>Reset Password</h2>
                    <p class="subtitle">Enter your new password below.</p>
                    <form method="POST" action="<?= Url::to("reset-password/{$token}") ?>">
                        <?= Csrf::field() ?>
                        <div class="mb-3">
                            <label for="password" class="ce-form-label">New Password</label>
                            <input type="password" class="form-control ce-form-control" id="password" name="password" required minlength="8">
                            <div class="form-text">Min 8 characters, with uppercase, lowercase, and number.</div>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="ce-form-label">Confirm New Password</label>
                            <input type="password" class="form-control ce-form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        <button type="submit" class="btn btn-ce-primary w-100">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
