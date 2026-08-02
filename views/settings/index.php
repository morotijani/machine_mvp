<?php
$title = "Company Settings";
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-xl-7">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3 mb-3">
            <h1 class="h3" style="color: #1f1f1f; font-weight: 400;">Company Settings</h1>
        </div>

        <style>
            .google-card {
                background: #fff;
                border-radius: 28px;
                overflow: hidden;
                margin-bottom: 24px;
                border: none;
                box-shadow: none;
            }
            .google-row {
                display: flex;
                align-items: center;
                padding: 24px 32px;
                border-bottom: 1px solid #e3e3e3;
                transition: background-color 0.2s;
            }
            .google-row:last-child {
                border-bottom: none;
            }
            .google-row:focus-within {
                background-color: #f8f9fa;
            }
            .google-icon {
                color: #444746;
                margin-right: 24px;
                font-size: 24px;
            }
            .google-content {
                flex-grow: 1;
                display: flex;
                flex-direction: column;
            }
            .google-label {
                font-size: 14px;
                color: #444746;
                margin-bottom: 4px;
                font-weight: 500;
            }
            .google-input, .google-textarea {
                border: none;
                outline: none;
                background: transparent;
                font-size: 16px;
                color: #1f1f1f;
                padding: 0;
                width: 100%;
            }
            .google-input:focus, .google-textarea:focus {
                color: #0b57d0;
            }
            .google-textarea {
                resize: none;
                margin-top: 2px;
            }
            .google-btn {
                background-color: #0b57d0;
                color: #fff;
                border-radius: 24px;
                padding: 10px 24px;
                font-weight: 500;
                border: none;
                transition: background-color 0.2s;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                text-decoration: none;
                font-size: 14px;
                cursor: pointer;
            }
            .google-btn:hover {
                background-color: #0842a0;
            }
            .google-alert {
                border-radius: 16px;
                border: none;
                padding: 12px 20px;
                display: flex;
                align-items: center;
                gap: 12px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                font-weight: 500;
                font-size: 14px;
                margin-bottom: 24px;
            }
            .google-alert-success {
                background-color: #e6f4ea;
                color: #137333;
            }
            .google-alert-danger {
                background-color: #fce8e6;
                color: #d93025;
            }
            
            /* custom file input wrapper */
            .file-upload-wrapper {
                position: relative;
                overflow: hidden;
                display: inline-block;
            }
            .file-upload-wrapper input[type=file] {
                font-size: 100px;
                position: absolute;
                left: 0;
                top: 0;
                opacity: 0;
                cursor: pointer;
                height: 100%;
            }
        </style>

        <?php if (isset($error)): ?>
            <div class="alert google-alert google-alert-danger alert-dismissible fade show" role="alert">
                <span class="material-symbols-outlined" style="font-size: 20px;">error</span>
                <div><?= e($error) ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert google-alert google-alert-success alert-dismissible fade show" role="alert">
                <span class="material-symbols-outlined" style="font-size: 20px;">check_circle</span>
                <div><?= e($success) ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/settings/update" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="google-card">
                
                <div class="google-row align-items-start py-4">
                    <span class="material-symbols-outlined google-icon mt-1">image</span>
                    <div class="google-content">
                        <label class="google-label mb-3">Company Logo</label>
                        <div class="d-flex align-items-center gap-4">
                            <?php if (!empty($settings['company_logo'])): ?>
                                <img src="<?= BASE_URL ?>/<?= $settings['company_logo'] ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 16px; border: 1px solid #e3e3e3;">
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border-radius: 16px; background-color: #f1f3f4; color: #444746;">
                                    <span class="material-symbols-outlined">storefront</span>
                                </div>
                            <?php endif; ?>
                            
                            <div>
                                <div class="file-upload-wrapper">
                                    <button type="button" class="btn btn-light" style="border-radius: 20px; font-weight: 500; background: #f1f3f4; border: none; padding: 6px 16px; font-size: 14px;">Change Logo</button>
                                    <input type="file" name="company_logo" accept="image/*">
                                </div>
                                <div class="text-muted mt-2" style="font-size: 12px;">Recommended: Square PNG/JPG</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">storefront</span>
                    <div class="google-content">
                        <label class="google-label">Company Name</label>
                        <input type="text" name="company_name" class="google-input" value="<?= e($settings['company_name'] ?? '') ?>" placeholder="Enter company name" required>
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">call</span>
                    <div class="google-content">
                        <label class="google-label">Phone Number</label>
                        <input type="text" name="company_phone" class="google-input" value="<?= e($settings['company_phone'] ?? '') ?>" placeholder="Enter contact number">
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">mail</span>
                    <div class="google-content">
                        <label class="google-label">Email Address</label>
                        <input type="email" name="company_email" class="google-input" value="<?= e($settings['company_email'] ?? '') ?>" placeholder="Enter email address">
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">location_on</span>
                    <div class="google-content">
                        <label class="google-label">Address (Street, City, State, Zip)</label>
                        <textarea name="company_address" class="google-textarea" rows="2" placeholder="Enter full address"><?= e($settings['company_address'] ?? '') ?></textarea>
                    </div>
                </div>
                
                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">receipt_long</span>
                    <div class="google-content">
                        <label class="google-label mb-2">Receipt Print Format</label>
                        <div class="d-flex gap-4 mt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="receipt_type" id="receipt_a4" value="a4" <?= (!isset($settings['receipt_type']) || $settings['receipt_type'] === 'a4') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="receipt_a4" style="color: #1f1f1f;">Standard A4</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="receipt_type" id="receipt_thermal" value="thermal" <?= (isset($settings['receipt_type']) && $settings['receipt_type'] === 'thermal') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="receipt_thermal" style="color: #1f1f1f;">Thermal POS Receipt (80mm)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">view_module</span>
                    <div class="google-content">
                        <label class="google-label mb-1">System Modules</label>
                        <div class="form-check form-switch mt-1">
                            <input class="form-check-input" type="checkbox" role="switch" id="enable_debt_module" name="enable_debt_module" value="1" <?= (!isset($settings['enable_debt_module']) || $settings['enable_debt_module'] == 1) ? 'checked' : '' ?> style="cursor: pointer;">
                            <label class="form-check-label" for="enable_debt_module" style="color: #1f1f1f;">Enable Standalone Debt Module</label>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 13px;">If turned off, the Debtors menu will be hidden and standalone debt will not be included in dashboard calculations.</div>
                    </div>
                </div>

            </div>

            <div class="d-flex justify-content-end mb-5">
                <button type="submit" class="google-btn">Save Settings</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
