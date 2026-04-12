<?php
$title = "Manage Users";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
            <h1 class="h2">Manage Users</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="<?= BASE_URL ?>/users/create" class="btn btn-sm btn-primary d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size: 20px;">add</span> New User
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
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
                                            <div class="fw-bold"><?= e($user['fullname'] ?? $user['username']) ?></div>
                                            <?php if (!empty($user['fullname'])): ?>
                                                <small class="text-muted">@<?= e($user['username']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                        $roleClass = 'bg-secondary';
                                        switch($user['role']) {
                                            case 'admin': $roleClass = 'bg-danger'; break;
                                            case 'sales': $roleClass = 'bg-primary'; break;
                                            case 'cashier': $roleClass = 'bg-success'; break;
                                            case 'sales_cashier': $roleClass = 'bg-info'; break;
                                        }
                                    ?>
                                    <span class="badge <?= $roleClass ?>">
                                        <?= e(str_replace('_', ' & ', ucfirst($user['role']))) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($user['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Disabled</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <div class="d-flex gap-2 justify-content-start">
                                        <form action="<?= BASE_URL ?>/users/toggle-status" method="POST" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <input type="hidden" name="status" value="<?= $user['is_active'] ? 0 : 1 ?>">
                                            <button type="submit" class="btn btn-sm <?= $user['is_active'] ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= $user['is_active'] ? 'Disable User' : 'Enable User' ?>">
                                                <span class="material-symbols-outlined" style="font-size: 16px;"><?= $user['is_active'] ? 'person_off' : 'person_check' ?></span>
                                            </button>
                                        </form>
                                        
                                        <a href="<?= BASE_URL ?>/users/edit?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Edit Profile">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">edit</span>
                                        </a>

                                        <div class="dropdown" style="display:inline;">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle no-caret" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Change Role">
                                                <span class="material-symbols-outlined" style="font-size: 16px;">manage_accounts</span>
                                            </button>
                                            <ul class="dropdown-menu shadow border-0 dropdown-menu-end">
                                                <li><h6 class="dropdown-header">Set Role To:</h6></li>
                                                <li>
                                                    <form action="<?= BASE_URL ?>/users/update-role" method="POST">
                                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <input type="hidden" name="role" value="sales">
                                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 <?= $user['role'] === 'sales' ? 'active' : '' ?>">
                                                            <span class="material-symbols-outlined text-primary" style="font-size: 18px;">person</span> Sales
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="<?= BASE_URL ?>/users/update-role" method="POST">
                                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <input type="hidden" name="role" value="cashier">
                                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 <?= $user['role'] === 'cashier' ? 'active' : '' ?>">
                                                            <span class="material-symbols-outlined text-success" style="font-size: 18px;">payments</span> Cashier
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="<?= BASE_URL ?>/users/update-role" method="POST">
                                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <input type="hidden" name="role" value="sales_cashier">
                                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 <?= $user['role'] === 'sales_cashier' ? 'active' : '' ?>">
                                                            <span class="material-symbols-outlined text-info" style="font-size: 18px;">point_of_sale</span> Sales & Cashier
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                     <form action="<?= BASE_URL ?>/users/update-role" method="POST">
                                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <input type="hidden" name="role" value="admin">
                                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 <?= $user['role'] === 'admin' ? 'active' : '' ?>">
                                                            <span class="material-symbols-outlined text-danger" style="font-size: 18px;">admin_panel_settings</span> Admin
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                        
                                        <form action="<?= BASE_URL ?>/users/delete" method="POST" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete User" onclick="return confirm('WARNING: Are you sure you want to delete this user? This will remove them from the active list but keep their sales history.')">
                                                <span class="material-symbols-outlined" style="font-size: 16px;">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
