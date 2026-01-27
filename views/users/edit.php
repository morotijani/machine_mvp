<?php
$title = "Edit User - " . $user['fullname'];
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white py-3 d-flex align-items-center gap-2">
                <a href="<?= BASE_URL ?>/admin/staff/detail?id=<?= $user['id'] ?>" class="btn btn-outline-secondary btn-sm rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <h5 class="card-title mb-0 fw-bold text-muted">Edit User Profile</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/users/update" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <div class="mb-3 position-relative d-inline-block shadow-sm rounded-circle p-1 bg-white" style="cursor: pointer;" onclick="document.getElementById('imgInput').click()">
                                <?php if ($user['profile_image']): ?>
                                    <img src="<?= BASE_URL ?>/<?= $user['profile_image'] ?>" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover; display: block;" id="preview">
                                <?php else: ?>
                                    <div id="placeholder" class="avatar-placeholder bg-light text-muted d-flex align-items-center justify-content-center mx-auto rounded-circle" style="width: 150px; height: 150px; font-size: 3rem;">
                                        <?= strtoupper(substr($user['fullname'], 0, 1)) ?>
                                    </div>
                                    <img src="" class="rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover; display: none;" id="preview">
                                <?php endif; ?>
                                <div class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <span class="material-symbols-outlined" style="font-size: 18px;">photo_camera</span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <input type="file" name="profile_image" class="d-none" id="imgInput" onchange="previewImage(this)" accept="image/*">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('imgInput').click()">Select New Photo</button>
                            </div>
                            <small class="text-muted d-block mt-2" style="font-size: 0.7rem;">PNG, JPG or WebP. Max 2MB.</small>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase">Full Name</label>
                                <input type="text" name="fullname" class="form-control" value="<?= e($user['fullname']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase">Username</label>
                                <input type="text" name="username" class="form-control" value="<?= e($user['username']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase">System Role</label>
                                <select name="role" class="form-select" <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                    <option value="sales" <?= $user['role'] === 'sales' ? 'selected' : '' ?>>Sales Role</option>
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin Role</option>
                                </select>
                                <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                    <input type="hidden" name="role" value="<?= $user['role'] ?>">
                                    <small class="text-danger mt-1 d-block" style="font-size: 0.7rem;">You cannot change your own role here.</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-danger">Change Password</label>
                        <input type="password" name="password" class="form-control mb-1" placeholder="Leave blank to keep current password">
                        <small class="text-muted">Minimum 6 characters recommended.</small>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                            <span class="material-symbols-outlined">save</span> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
