<?php
$title = "Company Settings";
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
            <h1 class="h2">Company Settings</h1>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= e($success) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?= BASE_URL ?>/settings/update" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <label class="form-label d-block fw-bold">Company Logo</label>
                            <?php if (!empty($settings['company_logo'])): ?>
                                <img src="<?= BASE_URL ?>/<?= $settings['company_logo'] ?>" class="img-thumbnail mb-2" style="max-height: 150px;">
                            <?php else: ?>
                                <div class="bg-light border rounded d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 150px; height: 150px;">
                                    <span class="text-muted">No Logo</span>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="company_logo" class="form-control form-control-sm mt-2" accept="image/*">
                            <div class="form-text">Recommended: Square PNG/JPG</div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company_name" class="form-control" value="<?= e($settings['company_name'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="company_phone" class="form-control" value="<?= e($settings['company_phone'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="company_email" class="form-control" value="<?= e($settings['company_email'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address (Street, City, State, Zip)</label>
                        <textarea name="company_address" class="form-control" rows="3"><?= e($settings['company_address'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
