<?php
$title = $user['fullname'] . " - Performance Analysis";
ob_start();

$totalRevenue = $stats['total'] ?: 0;
$totalCollected = $stats['collected'] ?: 0;
$totalProfit = $profit ?: 0;
$totalExpenses = $expenses ?: 0;
$netContribution = $totalProfit - $totalExpenses;
?>

<style>
    @media print {
        .no-print, header.navbar, #sidebarMenu { display: none !important; }
        .print-only { display: block !important; }
        body { background-color: #fff !important; margin: 0; padding: 0; }
        main { margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
        .row { display: flex; flex-wrap: wrap; }
        .col-md-2 { width: 16.666%; }
        .col-md-3 { width: 25%; }
        .col-md-4 { width: 33.333%; }
        .col-md-8 { width: 66.666%; }
        .card, .google-table-card, .google-stat-card { border: 1px solid #ddd !important; box-shadow: none !important; margin-bottom: 20px !important; }
        .bg-primary, .bg-dark, .bg-success, .bg-info { background-color: #fff !important; color: #000 !important; border: 1px solid #000 !important; }
        .text-white, .text-white-50 { color: #000 !important; }
        .badge, .google-pill { border: 1px solid #000 !important; color: #000 !important; background: #fff !important; }
    }
    .print-only { display: none; }
    
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
    .google-btn-sm {
        padding: 6px 16px;
        font-size: 13px;
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
    
    .google-stat-card {
        background: #fff;
        border-radius: 24px;
        padding: 24px 32px;
        border: none;
        box-shadow: none;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .google-stat-card.small-padding {
        padding: 24px 24px;
    }
    .google-pill {
        border-radius: 16px;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        text-transform: uppercase;
    }
    .google-pill-info { background-color: #e8f0fe; color: #0b57d0; }
    .google-pill-success { background-color: #e6f4ea; color: #188038; }
    .google-pill-warning { background-color: #fef7e0; color: #e37400; }
    .google-pill-danger { background-color: #fce8e6; color: #d93025; }
    .google-pill-light { background-color: #f1f3f4; color: #1f1f1f; }
</style>

<div class="row justify-content-center">
    <div class="col-12 col-xxl-11">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom pb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="<?= BASE_URL ?>/admin/staff" class="google-btn google-btn-outline" style="padding: 8px; border-radius: 50%; width: 40px; height: 40px; display: flex; justify-content: center; border: none; background: #f1f3f4;">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <?php 
                    $roleClass = 'info';
                    $roleLabel = ucfirst($user['role']);
                    switch($user['role']) {
                        case 'admin': $roleClass = 'light'; break;
                        case 'sales': $roleClass = 'info'; break;
                        case 'cashier': $roleClass = 'success'; break;
                        case 'sales_cashier': 
                            $roleClass = 'warning'; 
                            $roleLabel = 'Sales & Cashier';
                            break;
                    }
                ?>
                <?php if ($user['profile_image']): ?>
                    <img src="<?= BASE_URL ?>/<?= $user['profile_image'] ?>" class="rounded-circle" style="width: 48px; height: 48px; object-fit: cover;">
                <?php else: ?>
                    <div class="text-white d-flex align-items-center justify-content-center fw-medium" style="width: 48px; height: 48px; border-radius: 50%; background-color: <?= $user['role'] === 'admin' ? '#1f1f1f' : '#0b57d0' ?>;">
                        <?= strtoupper(substr($user['fullname'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <h1 class="h3 mb-0 fw-normal" style="color: #1f1f1f;"><?= e($user['fullname']) ?> 
                    <span class="google-pill google-pill-<?= $roleClass ?> ms-2"><?= e($roleLabel) ?></span>
                </h1>
            </div>
            <div class="d-flex align-items-center gap-3 no-print">
                <button onclick="window.print()" class="google-btn google-btn-primary">
                    <span class="material-symbols-outlined" style="font-size: 18px;">print</span> Print Report
                </button>
                <div class="text-muted small border-start ps-3">
                    Member since <?= date('F d, Y', strtotime($user['created_at'])) ?>
                </div>
            </div>
            <div class="text-muted small print-only">
                Report Generated: <?= date('F d, Y H:i') ?> | Member since <?= date('F d, Y', strtotime($user['created_at'])) ?>
            </div>
        </div>

        <!-- Today's Summary -->
        <div class="mb-5">
            <h6 class="text-muted text-uppercase small mb-3 d-flex align-items-center gap-2" style="letter-spacing: 0.5px; font-weight: 500;">
                <span class="material-symbols-outlined fs-5">calendar_today</span> Today's Performance
                <span class="google-pill google-pill-danger" style="padding: 2px 8px; font-size: 10px;">LIVE</span>
            </h6>
            <!-- Row 1: Volume & Cash Flow -->
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="google-stat-card small-padding">
                        <small class="text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px; font-weight: 500;">Sales Count</small>
                        <h3 class="mb-0 fw-normal mt-1" style="color: #1f1f1f;"><?= format_large_number($todayStats['count']) ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="google-stat-card small-padding border-start border-4" style="border-color: #0b57d0 !important;">
                        <small class="text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px; font-weight: 500;">Revenue</small>
                        <h3 class="mb-0 fw-normal mt-1" style="color: #0b57d0;">₵<?= format_large_number($todayStats['revenue']) ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="google-stat-card small-padding border-start border-4" style="border-color: #188038 !important;">
                        <small class="text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px; font-weight: 500;">In Hand (New Sales)</small>
                        <h3 class="mb-0 fw-normal mt-1" style="color: #188038;">₵<?= format_large_number($todayStats['collected']) ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="google-stat-card small-padding border-start border-4" style="border-color: #188038 !important;">
                        <small class="text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px; font-weight: 500;">Debt Recovered</small>
                        <h3 class="mb-0 fw-normal mt-1" style="color: #188038;">₵<?= format_large_number($todayStats['debt_collected']) ?></h3>
                        <div class="mt-2 text-muted" style="font-size: 12px;">From Past Invoices</div>
                    </div>
                </div>
            </div>
            
            <!-- Row 2: Profitability -->
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="google-stat-card small-padding border-start border-4" style="border-color: #1a73e8 !important;">
                        <small class="text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px; font-weight: 500;">Gross Profit</small>
                        <h3 class="mb-0 fw-normal mt-1" style="color: #1f1f1f;">₵<?= format_large_number($todayStats['profit']) ?></h3>
                        <div class="mt-2 text-muted" style="font-size: 12px;">Revenue - Cost Price</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="google-stat-card small-padding border-start border-4" style="border-color: #d93025 !important;">
                        <small class="text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px; font-weight: 500;">Expenses</small>
                        <h3 class="mb-0 fw-normal mt-1" style="color: #d93025;">₵<?= format_large_number($todayStats['expenses']) ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="google-stat-card small-padding border-start border-4" style="border-color: #1f1f1f !important;">
                        <small class="text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px; font-weight: 500;">Net Today</small>
                        <h3 class="mb-0 fw-normal mt-1 <?= $todayStats['net'] < 0 ? 'text-danger' : 'text-success' ?>">₵<?= format_large_number($todayStats['net']) ?></h3>
                        <div class="mt-2 text-muted" style="font-size: 12px;">Gross Profit - Expenses</div>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="text-muted text-uppercase small mb-3 d-flex align-items-center gap-2" style="letter-spacing: 0.5px; font-weight: 500;">
            <span class="material-symbols-outlined fs-5">monitoring</span> Account Lifetime stats
        </h6>
        <!-- KPI Cards -->
        <div class="row g-3 mb-5">
            <div class="col-md-3">
                <div class="google-stat-card" style="background-color: #f1f3f4;">
                    <h6 class="text-muted text-uppercase small" style="letter-spacing: 0.5px; font-weight: 500;">Lifetime Revenue</h6>
                    <h3 class="mb-0 fw-normal mt-1" style="color: #1f1f1f;">₵<?= format_large_number($totalRevenue) ?></h3>
                    <div class="mt-2 text-muted" style="font-size: 12px;">Total Invoices Generated</div>
                    <div class="text-muted mt-1" style="font-size: 12px;"><?= format_large_number($stats['count']) ?> Completed Sales</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="google-stat-card" style="background-color: #e6f4ea;">
                    <h6 class="text-muted text-uppercase small" style="color: #188038 !important; letter-spacing: 0.5px; font-weight: 500;">Actual Collected</h6>
                    <h3 class="mb-0 fw-normal mt-1" style="color: #188038;">₵<?= format_large_number($totalCollected) ?></h3>
                    <div class="mt-2" style="font-size: 12px; color: #188038;"><?= $totalRevenue > 0 ? number_format(($totalCollected/$totalRevenue)*100, 1) : 0 ?>% Collection Rate</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="google-stat-card" style="background-color: #e8f0fe;">
                    <h6 class="text-muted text-uppercase small" style="color: #0b57d0 !important; letter-spacing: 0.5px; font-weight: 500;">Total Gross Profit</h6>
                    <h3 class="mb-0 fw-normal mt-1" style="color: #0b57d0;">₵<?= format_large_number($totalProfit) ?></h3>
                    <div class="mt-2" style="font-size: 12px; color: #0b57d0;">Revenue - Cost Price</div>
                    <div class="mt-1" style="font-size: 12px; color: #0b57d0;"><?= $totalRevenue > 0 ? number_format(($totalProfit/$totalRevenue)*100, 1) : 0 ?>% Gross Margin</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="google-stat-card" style="background-color: #f1f3f4;">
                    <h6 class="text-muted text-uppercase small" style="letter-spacing: 0.5px; font-weight: 500;">Net Contribution</h6>
                    <h3 class="mb-0 fw-normal mt-1" style="color: <?= $netContribution < 0 ? '#d93025' : '#1f1f1f' ?>;">₵<?= format_large_number($netContribution) ?></h3>
                    <div class="mt-2 text-muted" style="font-size: 12px;">Profit minus Expenses</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Sales and Expenses -->
            <div class="col-lg-8">
                <!-- Recent Sales -->
                <div class="google-table-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined text-muted">shopping_cart</span> Recent Sales Activity
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-end no-print">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentSales as $sale): 
                                        $isToday = date('Y-m-d', strtotime($sale['created_at'])) === date('Y-m-d');
                                    ?>
                                    <tr style="<?= $isToday ? 'background-color: #fef7e0;' : '' ?>">
                                        <td style="color: #1f1f1f;">
                                            <?= date('M j, Y H:i', strtotime($sale['created_at'])) ?>
                                            <?php if ($isToday): ?>
                                                <span class="google-pill google-pill-warning ms-2" style="font-size: 10px; padding: 2px 6px;">TODAY</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $pillClass = $sale['payment_status'] === 'paid' ? 'success' : ($sale['payment_status'] === 'partial' ? 'warning' : 'danger'); 
                                            ?>
                                            <span class="google-pill google-pill-<?= $pillClass ?>">
                                                <?= ucfirst($sale['payment_status']) ?>
                                            </span>
                                        </td>
                                        <td class="text-end fw-medium" style="color: #1f1f1f;">₵<?= format_large_number($sale['total_amount']) ?></td>
                                        <td class="text-end no-print">
                                            <a href="<?= BASE_URL ?>/sales/view?id=<?= $sale['id'] ?>" class="google-btn google-btn-outline google-btn-sm">View Invoice</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($recentSales)): ?>
                                    <tr><td colspan="4" class="text-center py-5 text-muted">No recent sales found</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Expenses -->
                <div class="google-table-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined text-muted">payments</span> Recorded Expenses
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->prepare("SELECT * FROM expenditures WHERE recorded_by = ? AND is_deleted = 0 ORDER BY date DESC LIMIT 10");
                                    $stmt->execute([$uid]);
                                    $recentExpenses = $stmt->fetchAll();
                                    foreach ($recentExpenses as $exp): ?>
                                    <tr>
                                        <td style="color: #1f1f1f;"><?= date('M j, Y', strtotime($exp['date'])) ?></td>
                                        <td><span class="google-pill google-pill-light"><?= e($exp['category']) ?></span></td>
                                        <td class="text-muted"><?= e($exp['description']) ?></td>
                                        <td class="text-end" style="color: #d93025; font-weight: 500;">₵<?= format_large_number($exp['amount']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($recentExpenses)): ?>
                                    <tr><td colspan="4" class="text-center py-5 text-muted">No expenses recorded</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Login History and Info -->
            <div class="col-lg-4">
                <div class="google-table-card mb-4 no-print">
                    <div class="card-header">
                        <h5 class="mb-0 d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined text-muted">login</span> Login History
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush border-0">
                            <?php foreach ($loginHistory as $log): ?>
                            <div class="list-group-item border-bottom py-3" style="border-color: #e3e3e3 !important;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span style="font-weight: 500; color: #1f1f1f; font-size: 14px;"><?= date('M j, Y', strtotime($log['login_at'])) ?></span>
                                    <span class="text-muted" style="font-size: 13px;"><?= date('H:i', strtotime($log['login_at'])) ?></span>
                                </div>
                                <div class="text-muted mb-1 d-flex align-items-center gap-1" style="font-size: 13px;">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">language</span>
                                    IP: <?= e($log['ip_address']) ?>
                                </div>
                                <div class="text-muted text-truncate" style="font-size: 12px;" title="<?= e($log['user_agent']) ?>">
                                    <?= e(substr($log['user_agent'], 0, 50)) ?>...
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($loginHistory)): ?>
                            <div class="list-group-item text-center py-5 text-muted border-0">No login history recorded</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top py-3 text-center" style="border-color: #e3e3e3 !important;">
                        <a href="#" class="text-decoration-none" style="color: #0b57d0; font-weight: 500; font-size: 14px;">View All History</a>
                    </div>
                </div>

                <div class="google-stat-card p-4 no-print" style="background-color: #f1f3f4; justify-content: flex-start;">
                    <h6 class="mb-3 text-uppercase" style="font-weight: 500; letter-spacing: 0.5px; color: #1f1f1f; font-size: 13px;">Account Security</h6>
                    <div class="d-grid gap-3">
                        <form action="<?= BASE_URL ?>/users/update-role" method="POST" class="m-0">
                            <input type="hidden" name="user_id" value="<?= $uid ?>">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
                            <div class="d-flex gap-2">
                                <select name="role" class="form-select flex-grow-1" style="border-radius: 20px; font-size: 14px;" <?= $uid == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                    <option value="sales" <?= $user['role'] === 'sales' ? 'selected' : '' ?>>Sales Role</option>
                                    <option value="cashier" <?= $user['role'] === 'cashier' ? 'selected' : '' ?>>Cashier Role</option>
                                    <option value="sales_cashier" <?= $user['role'] === 'sales_cashier' ? 'selected' : '' ?>>Sales & Cashier</option>
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin Role</option>
                                </select>
                                <button class="google-btn google-btn-primary google-btn-sm" type="submit" <?= $uid == $_SESSION['user_id'] ? 'disabled' : '' ?>>Update</button>
                            </div>
                            <?php if ($uid == $_SESSION['user_id']): ?>
                            <small class="text-danger mt-2 d-block" style="font-size: 12px;">You cannot change your own role.</small>
                            <?php endif; ?>
                        </form>
                        <a href="<?= BASE_URL ?>/users/edit?id=<?= $uid ?>" class="google-btn google-btn-outline w-100 justify-content-center">Edit User Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>
