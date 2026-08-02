<?php
$title = "Manage Users";
ob_start();
?>
<style>
    .google-add-btn {
        background-color: #3b71ca;
        color: #fff;
        border: none;
        border-radius: 22px;
        height: 44px;
        padding: 0 24px;
        font-weight: 500;
        font-size: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: background-color 0.2s;
    }
    .google-add-btn:hover {
        background-color: #2b5db0;
        color: #fff;
    }

    .google-table-card {
        background: #fff;
        border-radius: 24px;
        overflow: hidden;
        border: none;
        box-shadow: none;
        margin-bottom: 30px;
    }
    .google-table-card thead th {
        border-bottom: 1px solid #e3e3e3;
        background-color: #fff;
        color: #5f6368;
        font-weight: 500;
        padding: 16px 24px;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .google-table-card tbody td {
        padding: 16px 24px;
        border-bottom: 1px solid #e3e3e3;
        color: #1f1f1f;
        vertical-align: middle;
    }
    .google-table-card tbody tr:last-child td {
        border-bottom: none;
    }
    .google-table-card tbody tr {
        transition: background-color 0.2s;
    }
    .google-table-card tbody tr:hover {
        background-color: #f8f9fa;
    }
    .action-btn {
        border: none;
        background: transparent;
        color: #5f6368;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s, color 0.2s;
        text-decoration: none !important;
    }
    .action-btn:hover {
        background-color: #f1f3f4;
        color: #202124;
    }

    /* Modal styling */
    .material-modal .modal-content {
        border-radius: 28px;
        border: none;
        box-shadow: 0 4px 24px rgba(0,0,0,0.15);
    }
    .material-modal .modal-title-custom {
        font-size: 24px;
        color: #1f1f1f;
        font-weight: 400;
        margin-bottom: 16px;
    }
    .material-modal .btn-cancel {
        color: #0b57d0;
        font-weight: 500;
        text-decoration: none;
        padding: 10px 16px;
        border-radius: 20px;
    }
    .material-modal .btn-cancel:hover {
        background-color: #f6f8fb;
    }
    .material-modal .btn-ok {
        background-color: #0b57d0;
        color: #fff;
        font-weight: 500;
        border-radius: 20px;
        padding: 10px 24px;
        border: none;
        transition: background-color 0.2s;
    }
</style>

<div class="row justify-content-center">
    <div class="col-12 col-xxl-11">
        <div class="d-flex justify-content-between flex-wrap align-items-center pt-4 pb-3 mb-3 gap-2">
            <h1 class="h3 mb-0" style="color: #1f1f1f; font-weight: 400;">Manage Users</h1>
            <div class="d-flex flex-wrap gap-2 page-header-actions">
                <a href="<?= BASE_URL ?>/users/create" class="google-add-btn shadow-sm">
                    <span class="material-symbols-outlined" style="font-size: 20px;">add</span> New User
                </a>
            </div>
        </div>

        <div class="google-table-card">
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($user['profile_image'])): ?>
                                        <img src="<?= BASE_URL ?>/<?php echo $user['profile_image']; ?>" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="rounded-circle me-3 bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center fw-bold text-secondary" style="width: 40px; height: 40px;">
                                            <?= e(strtoupper(substr($user['username'], 0, 1))) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-bold text-dark"><?= e($user['fullname'] ?? $user['username']) ?></div>
                                        <?php if (!empty($user['fullname'])): ?>
                                            <small class="text-muted">@<?= e($user['username']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php 
                                    $roleBadge = '';
                                    switch($user['role']) {
                                        case 'admin': $roleBadge = 'bg-danger bg-opacity-10 text-danger border-danger-subtle'; break;
                                        case 'sales': $roleBadge = 'bg-primary bg-opacity-10 text-primary border-primary-subtle'; break;
                                        case 'cashier': $roleBadge = 'bg-success bg-opacity-10 text-success border-success-subtle'; break;
                                        case 'sales_cashier': $roleBadge = 'bg-info bg-opacity-10 text-info border-info-subtle'; break;
                                    }
                                ?>
                                <span class="badge border rounded-pill px-3 py-1 fw-medium <?= $roleBadge ?>">
                                    <?= e(str_replace('_', ' & ', ucfirst($user['role']))) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user['is_active']): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3 py-1 fw-medium">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle rounded-pill px-3 py-1 fw-medium">Disabled</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted fw-bold" style="font-size: 13px;"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td class="text-end text-nowrap">
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <div class="d-flex justify-content-end gap-1">
                                    <form action="<?= BASE_URL ?>/users/toggle-status" method="POST" class="m-0">
                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <input type="hidden" name="status" value="<?= $user['is_active'] ? 0 : 1 ?>">
                                        <button type="submit" class="action-btn <?= $user['is_active'] ? 'text-warning' : 'text-success' ?>" title="<?= $user['is_active'] ? 'Disable User' : 'Enable User' ?>">
                                            <span class="material-symbols-outlined" style="font-size: 20px;"><?= $user['is_active'] ? 'person_off' : 'person_check' ?></span>
                                        </button>
                                    </form>
                                    
                                    <a href="<?= BASE_URL ?>/users/edit?id=<?= $user['id'] ?>" class="action-btn text-secondary" title="Edit Profile">
                                        <span class="material-symbols-outlined" style="font-size: 20px;">edit</span>
                                    </a>

                                    <div class="dropdown" style="display:inline;">
                                        <button class="action-btn text-primary dropdown-toggle no-caret" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Change Role">
                                            <span class="material-symbols-outlined" style="font-size: 20px;">manage_accounts</span>
                                        </button>
                                        <ul class="dropdown-menu shadow-sm border-0 dropdown-menu-end" style="border-radius: 16px; padding: 8px;">
                                            <li><h6 class="dropdown-header">Set Role To:</h6></li>
                                            <li>
                                                <form action="<?= BASE_URL ?>/users/update-role" method="POST">
                                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                    <input type="hidden" name="role" value="sales">
                                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2 rounded-3 <?= $user['role'] === 'sales' ? 'active' : '' ?>">
                                                        <span class="material-symbols-outlined text-primary" style="font-size: 18px;">person</span> Sales
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="<?= BASE_URL ?>/users/update-role" method="POST">
                                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                    <input type="hidden" name="role" value="cashier">
                                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2 rounded-3 <?= $user['role'] === 'cashier' ? 'active' : '' ?>">
                                                        <span class="material-symbols-outlined text-success" style="font-size: 18px;">payments</span> Cashier
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="<?= BASE_URL ?>/users/update-role" method="POST">
                                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                    <input type="hidden" name="role" value="sales_cashier">
                                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2 rounded-3 <?= $user['role'] === 'sales_cashier' ? 'active' : '' ?>">
                                                        <span class="material-symbols-outlined text-info" style="font-size: 18px;">point_of_sale</span> Sales & Cashier
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                 <form action="<?= BASE_URL ?>/users/update-role" method="POST">
                                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                    <input type="hidden" name="role" value="admin">
                                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2 rounded-3 <?= $user['role'] === 'admin' ? 'active' : '' ?>">
                                                        <span class="material-symbols-outlined text-danger" style="font-size: 18px;">admin_panel_settings</span> Admin
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <form id="deleteForm<?= $user['id'] ?>" action="<?= BASE_URL ?>/users/delete" method="POST" class="m-0">
                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="button" class="action-btn text-danger" title="Delete User" onclick="confirmDelete(<?= $user['id'] ?>)">
                                            <span class="material-symbols-outlined" style="font-size: 20px;">delete</span>
                                        </button>
                                    </form>
                                </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($users)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">No users found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade material-modal" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content">
            <div class="modal-body p-4">
                <h4 class="modal-title-custom text-danger d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined">warning</span> Delete User?
                </h4>
                <p class="modal-text text-muted mb-4">Are you sure you want to delete this user? This will remove them from the active list but keep their sales history.</p>
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <button type="button" class="btn btn-link btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-ok bg-danger text-white" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentDeleteId = null;
function confirmDelete(id) {
    currentDeleteId = id;
    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    modal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if(currentDeleteId) {
        document.getElementById('deleteForm' + currentDeleteId).submit();
    }
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
