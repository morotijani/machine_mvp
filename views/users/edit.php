<?php
$title = "Edit User - " . $user['fullname'];
ob_start();
?>



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
        font-size: 16px;
        color: #1f1f1f;
        margin-bottom: 4px;
        font-weight: 500;
    }
    .google-input, .google-select {
        border: none;
        outline: none;
        background: transparent;
        font-size: 15px;
        color: #5f6368;
        padding: 0;
        width: 100%;
    }
    .google-input:focus, .google-select:focus {
        color: #202124;
    }
    .btn-save {
        background-color: #0b57d0;
        color: #fff;
        border-radius: 24px;
        padding: 10px 24px;
        font-weight: 500;
        border: none;
        transition: background-color 0.2s;
    }
    .btn-save:hover {
        background-color: #0842a0;
    }
</style>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3 mb-3">
            <h1 class="h3 mb-0" style="color: #1f1f1f; font-weight: 400;">Edit User Profile</h1>
            <a href="<?= BASE_URL ?>/users" class="btn btn-light d-flex align-items-center gap-2 text-decoration-none" style="border-radius: 20px; font-weight: 500;">
                <span class="material-symbols-outlined fs-6">arrow_back</span> Back to List
            </a>
        </div>

        <form action="<?= BASE_URL ?>/users/update" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div class="google-card">
                <!-- Profile Picture -->
                <div class="text-center py-4 border-bottom" style="border-color: #e3e3e3 !important;">
                    <div class="position-relative d-inline-block shadow-sm rounded-circle p-1 bg-white" style="cursor: pointer;" onclick="document.getElementById('imgInput').click()">
                        <?php if ($user['profile_image']): ?>
                            <img src="<?= BASE_URL ?>/<?= $user['profile_image'] ?>" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover; display: block;" id="preview">
                        <?php else: ?>
                            <div id="placeholder" class="avatar-placeholder bg-light text-muted d-flex align-items-center justify-content-center mx-auto rounded-circle" style="width: 120px; height: 120px; font-size: 3rem;">
                                <?= strtoupper(substr($user['fullname'], 0, 1)) ?>
                            </div>
                            <img src="" class="rounded-circle shadow-sm" style="width: 120px; height: 120px; object-fit: cover; display: none;" id="preview">
                        <?php endif; ?>
                        <div class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; transform: translate(-10%, -10%);">
                            <span class="material-symbols-outlined" style="font-size: 16px;">photo_camera</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <input type="file" name="profile_image" class="d-none" id="imgInput" onchange="previewImage(this)" accept="image/*">
                        <span class="text-primary fw-medium" style="cursor: pointer;" onclick="document.getElementById('imgInput').click()">Change Photo</span>
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">badge</span>
                    <div class="google-content">
                        <label class="google-label">Full Name</label>
                        <input type="text" name="fullname" class="google-input" value="<?= e($user['fullname']) ?>" required>
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">alternate_email</span>
                    <div class="google-content">
                        <label class="google-label">Username</label>
                        <input type="text" name="username" class="google-input" value="<?= e($user['username']) ?>" required>
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">admin_panel_settings</span>
                    <div class="google-content">
                        <label class="google-label">System Role</label>
                        <select name="role" class="google-select" required style="margin-top: 2px;" <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                            <option value="sales" <?= $user['role'] === 'sales' ? 'selected' : '' ?>>Sales (Pure)</option>
                            <option value="cashier" <?= $user['role'] === 'cashier' ? 'selected' : '' ?>>Cashier (Pure)</option>
                            <option value="sales_cashier" <?= $user['role'] === 'sales_cashier' ? 'selected' : '' ?>>Sales & Cashier (Combined)</option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrator</option>
                        </select>
                        <?php if ($user['id'] == $_SESSION['user_id']): ?>
                            <input type="hidden" name="role" value="<?= $user['role'] ?>">
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Security Section -->
                <div class="google-row">
                    <span class="material-symbols-outlined google-icon text-danger">lock</span>
                    <div class="google-content">
                        <label class="google-label text-danger">Change Password</label>
                        <input type="password" name="password" class="google-input" placeholder="Leave blank to keep current password">
                    </div>
                </div>

            </div>

            <div class="d-flex justify-content-end mb-5">
                <button type="submit" class="btn-save">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            let preview = document.getElementById('preview');
            let placeholder = document.getElementById('placeholder');
            
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (placeholder) {
                    placeholder.style.display = 'none';
                }
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
