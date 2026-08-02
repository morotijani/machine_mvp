<?php
$title = "My Profile";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-8 col-xl-7">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3 mb-3">
            <h1 class="h3" style="color: #1f1f1f; font-weight: 400;">My Profile</h1>
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
            .google-input {
                border: none;
                outline: none;
                background: transparent;
                font-size: 16px;
                color: #1f1f1f;
                padding: 0;
                width: 100%;
            }
            .google-input:focus {
                color: #0b57d0;
            }
            .google-input:disabled {
                color: #5f6368;
                background-color: transparent;
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

        <form action="<?= BASE_URL ?>/profile/update" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <h5 style="color: #1f1f1f; font-weight: 400; font-size: 16px; margin-bottom: 16px; margin-left: 16px;">Basic Information</h5>
            
            <div class="google-card">
                <div class="google-row align-items-start py-4">
                    <span class="material-symbols-outlined google-icon mt-1">account_circle</span>
                    <div class="google-content">
                        <label class="google-label mb-3">Profile Picture</label>
                        <div class="d-flex align-items-center gap-4">
                            <?php if (!empty($user['profile_image'])): ?>
                                <img src="<?= BASE_URL ?>/<?php echo $user['profile_image']; ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; border: 1px solid #e3e3e3;">
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border-radius: 50%; background-color: #f1f3f4; color: #444746; font-size: 24px; font-weight: 500;">
                                    <?= e(strtoupper(substr($user['username'], 0, 1))) ?>
                                </div>
                            <?php endif; ?>
                            
                            <div>
                                <div class="file-upload-wrapper">
                                    <button type="button" class="btn btn-light" style="border-radius: 20px; font-weight: 500; background: #f1f3f4; border: none; padding: 6px 16px; font-size: 14px;">Change Picture</button>
                                    <input type="file" name="profile_image" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">badge</span>
                    <div class="google-content">
                        <label class="google-label">Full Name</label>
                        <input type="text" name="fullname" class="google-input" value="<?= e($user['fullname'] ?? '') ?>" placeholder="Enter full name">
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">alternate_email</span>
                    <div class="google-content">
                        <label class="google-label">Username</label>
                        <input type="text" class="google-input" value="<?= e($user['username']) ?>" readonly disabled>
                    </div>
                </div>
            </div>
            
            <h5 style="color: #1f1f1f; font-weight: 400; font-size: 16px; margin-bottom: 16px; margin-left: 16px; margin-top: 32px;">Security</h5>

            <div class="google-card">
                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">lock</span>
                    <div class="google-content">
                        <label class="google-label">New Password</label>
                        <input type="password" name="new_password" class="google-input" placeholder="Leave blank to keep current password">
                    </div>
                </div>
                
                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">password</span>
                    <div class="google-content">
                        <label class="google-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="google-input" placeholder="Re-type new password">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-5">
                <button type="submit" class="google-btn">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
