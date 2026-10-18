<div class="row">
    <div class="col-lg-8">
        <div class="ce-card">
            <div class="ce-card-header">Personal Information</div>
            <div class="ce-card-body">
                <form method="POST" action="<?= Url::to('profile') ?>">
                    <?= Csrf::field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="ce-form-label">First Name</label>
                            <input type="text" class="form-control ce-form-control" value="<?= Escape::html($user['first_name'] ?? '') ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="ce-form-label">Last Name</label>
                            <input type="text" class="form-control ce-form-control" value="<?= Escape::html($user['last_name'] ?? '') ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="ce-form-label">Email</label>
                            <input type="email" class="form-control ce-form-control" value="<?= Escape::html($user['email'] ?? '') ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="ce-form-label">Phone</label>
                            <input type="tel" class="form-control ce-form-control" name="phone" value="<?= Escape::html($user['phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="ce-form-label">Date of Birth</label>
                            <input type="date" class="form-control ce-form-control" name="date_of_birth" value="<?= Escape::html($profile['date_of_birth'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="ce-form-label">Nationality</label>
                            <input type="text" class="form-control ce-form-control" name="nationality" value="<?= Escape::html($profile['nationality'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="ce-form-label">Address</label>
                            <textarea class="form-control ce-form-control" name="address" rows="2"><?= Escape::html($profile['address'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="ce-form-label">City</label>
                            <input type="text" class="form-control ce-form-control" name="city" value="<?= Escape::html($profile['city'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="ce-form-label">Country</label>
                            <input type="text" class="form-control ce-form-control" name="country" value="<?= Escape::html($profile['country'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="ce-form-label">Postal Code</label>
                            <input type="text" class="form-control ce-form-control" name="postal_code" value="<?= Escape::html($profile['postal_code'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="ce-form-label">Occupation</label>
                            <input type="text" class="form-control ce-form-control" name="occupation" value="<?= Escape::html($profile['occupation'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="ce-form-label">Source of Funds</label>
                            <input type="text" class="form-control ce-form-control" name="source_of_funds" value="<?= Escape::html($profile['source_of_funds'] ?? '') ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-ce-primary mt-4">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>
