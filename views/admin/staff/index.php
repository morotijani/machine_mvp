<?php
$title = "Staff Performance";
ob_start();
?>

<style>
    .google-btn {
        border-radius: 24px;
        padding: 8px 24px;
        font-weight: 500;
        border: none;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        font-size: 14px;
    }
    .google-btn-primary {
        background-color: #0b57d0;
        color: #fff;
    }
    .google-btn-primary:hover {
        background-color: #0842a0;
        color: #fff;
    }
    .google-btn-outline {
        background-color: transparent;
        color: #0b57d0;
        border: 1px solid #c7d0dd;
    }
    .google-btn-outline:hover {
        background-color: #f6f8fb;
    }
    .google-table-card {
        background: #fff;
        border-radius: 24px;
        overflow: hidden;
        border: none;
        box-shadow: none;
        margin-bottom: 24px;
    }
    .google-table-card .card-header {
        background-color: #fff;
        border-bottom: 1px solid #e3e3e3;
        padding: 24px 32px;
    }
    .google-table-card .card-header h5 {
        color: #1f1f1f;
        font-weight: 400;
        margin: 0;
    }
    .google-table-card .card-body {
        padding: 0;
    }
    .google-table-card table {
        margin-bottom: 0;
        width: 100%;
    }
    .google-table-card thead th {
        border-bottom: 1px solid #e3e3e3;
        background-color: #fff;
        color: #5f6368;
        font-weight: 500;
        padding: 16px 32px;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .google-table-card tbody td {
        padding: 16px 32px;
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
    .google-pill {
        border-radius: 16px;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
    }
    .google-pill-info {
        background-color: #e8f0fe;
        color: #0b57d0;
    }
    .google-pill-light {
        background-color: #f1f3f4;
        color: #444746;
    }
</style>

<div class="row justify-content-center">
    <div class="col-12 col-xxl-11">
        <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-4 gap-2">
            <h1 class="h3 mb-0" style="color: #1f1f1f; font-weight: 400;">Staff Performance Dashboard</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="<?= BASE_URL ?>/users" class="google-btn google-btn-outline">
                    <span class="material-symbols-outlined" style="font-size: 18px;">group</span> Manage Users
                </a>
            </div>
        </div>

        <div class="google-table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">System Users Performance</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Name / Details</th>
                                <th>Role</th>
                                <th class="text-center">Transactions</th>
                                <th class="text-end">Total Revenue</th>
                                <th class="text-end">Cash Collected</th>
                                <th class="text-end">Expenses Recorded</th>
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
                                            <img src="<?= BASE_URL ?>/<?= $s['profile_image'] ?>" class="me-3 rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="me-3 text-white d-flex align-items-center justify-content-center fw-medium" style="width: 40px; height: 40px; border-radius: 50%; background-color: <?= $s['role'] === 'admin' ? '#1f1f1f' : '#0b57d0' ?>;">
                                                <?= strtoupper(substr($s['fullname'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div style="font-weight: 500; color: #1f1f1f;"><?= e($s['fullname']) ?></div>
                                                <span class="google-pill google-pill-light" style="padding: 2px 8px; font-size: 10px; letter-spacing: 0.5px;">@<?= e($s['username']) ?></span>
                                            </div>
                                            <small class="text-muted">Joined <?= date('M Y', strtotime($s['created_at'])) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="google-pill" style="<?= $s['role'] === 'admin' ? 'background-color: #f1f3f4; color: #1f1f1f;' : 'background-color: #e8f0fe; color: #0b57d0;' ?> text-transform: uppercase;">
                                        <?= e($s['role']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="google-pill google-pill-info"><?= $s['sales_count'] ?></span>
                                </td>
                                <td class="text-end fw-medium" style="color: #1f1f1f;">₵<?= format_large_number($revenue) ?></td>
                                <td class="text-end fw-medium" style="color: #188038;">₵<?= format_large_number($collected) ?></td>
                                <td class="text-end" style="color: #d93025;">₵<?= format_large_number($expenses) ?></td>
                                <td class="text-end">
                                    <a href="<?= BASE_URL ?>/admin/staff/detail?id=<?= $s['id'] ?>" class="google-btn google-btn-outline" style="padding: 6px 16px;">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">analytics</span> Analysis
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($staff)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <span class="material-symbols-outlined d-block mb-2" style="font-size: 48px; color: #c7d0dd;">person_off</span>
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
