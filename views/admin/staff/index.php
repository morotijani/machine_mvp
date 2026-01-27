<?php
$title = "Staff Performance";
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-11">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
            <h1 class="h2">Staff Performance Dashboard</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="<?= BASE_URL ?>/users" class="btn btn-sm btn-outline-secondary">
                    <span class="material-symbols-outlined align-middle fs-6">group</span> Manage Users
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold text-muted">System Users Performance</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name / Details</th>
                                <th>Role</th>
                                <th>Username</th>
                                <th class="text-center">Transactions</th>
                                <th class="text-end">Total Revenue</th>
                                <th class="text-end">Cash Collected</th>
                                <th class="text-end text-danger">Expenses Recorded</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staff as $s): 
                                $revenue = $s['total_revenue'] ?: 0;
                                $collected = $s['total_collected'] ?: 0;
                                $expenses = $s['total_expenses'] ?: 0;
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if ($s['profile_image']): ?>
                                            <img src="<?= BASE_URL ?>/<?= $s['profile_image'] ?>" class="me-3 rounded-circle shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="avatar-circle me-3 bg-<?= $s['role'] === 'admin' ? 'dark' : 'primary' ?> text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; border-radius: 50%;">
                                                <?= strtoupper(substr($s['fullname'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold"><?= e($s['fullname']) ?></div>
                                            <small class="text-muted">Joined <?= date('M Y', strtotime($s['created_at'])) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $s['role'] === 'admin' ? 'dark' : 'primary' ?> bg-opacity-10 text-<?= $s['role'] === 'admin' ? 'dark' : 'primary' ?> text-uppercase smaller">
                                        <?= e($s['role']) ?>
                                    </span>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= e($s['username']) ?></span></td>
                                <td class="text-center">
                                    <span class="badge bg-info bg-opacity-10 text-info px-3"><?= $s['sales_count'] ?></span>
                                </td>
                                <td class="text-end fw-bold">₵<?= number_format($revenue, 2) ?></td>
                                <td class="text-end text-success fw-bold">₵<?= number_format($collected, 2) ?></td>
                                <td class="text-end text-danger">₵<?= number_format($expenses, 2) ?></td>
                                <td class="text-end">
                                    <a href="<?= BASE_URL ?>/admin/staff/detail?id=<?= $s['id'] ?>" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">
                                        <span class="material-symbols-outlined fs-6">analytics</span> Analysis
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($staff)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <span class="material-symbols-outlined d-block mb-2" style="font-size: 48px;">person_off</span>
                                    No sales staff found.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>
