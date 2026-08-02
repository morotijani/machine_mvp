<?php
$title = "Create User";
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

    .google-input,
    .google-select {
        border: none;
        outline: none;
        background: transparent;
        font-size: 15px;
        color: #5f6368;
        padding: 0;
        width: 100%;
    }

    .google-input:focus,
    .google-select:focus {
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
            <h1 class="h3 mb-0" style="color: #1f1f1f; font-weight: 400;">Create New User</h1>
            <a href="<?= BASE_URL ?>/users" class="btn btn-light d-flex align-items-center gap-2 text-decoration-none"
                style="border-radius: 20px; font-weight: 500;">
                <span class="material-symbols-outlined fs-6">arrow_back</span> Back to List
            </a>
        </div>

        <form action="<?= BASE_URL ?>/users/create" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <div class="google-card">

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">badge</span>
                    <div class="google-content">
                        <label class="google-label">Full Name</label>
                        <input type="text" name="fullname" class="google-input" placeholder="Hamza Zero">
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">alternate_email</span>
                    <div class="google-content">
                        <label class="google-label">Username</label>
                        <input type="text" name="username" class="google-input" required placeholder="hamzazero">
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">image</span>
                    <div class="google-content">
                        <label class="google-label">Profile Picture</label>
                        <input type="file" name="profile_image" class="google-input" accept="image/*"
                            style="margin-top: 4px;">
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">lock</span>
                    <div class="google-content">
                        <label class="google-label">Password</label>
                        <input type="password" name="password" class="google-input" required placeholder="********">
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">admin_panel_settings</span>
                    <div class="google-content">
                        <label class="google-label">System Role</label>
                        <select name="role" class="google-select" required style="margin-top: 2px;">
                            <option value="sales">Sales (Pure)</option>
                            <option value="cashier">Cashier (Pure)</option>
                            <option value="sales_cashier">Sales & Cashier (Combined)</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                </div>

            </div>

            <div class="d-flex justify-content-end mb-5">
                <button type="submit" class="btn-save">Create User</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>