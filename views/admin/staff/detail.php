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
        .card { border: 1px solid #ddd !important; box-shadow: none !important; margin-bottom: 20px !important; }
        .bg-primary, .bg-dark, .bg-success, .bg-info { background-color: #fff !important; color: #000 !important; border: 1px solid #000 !important; }
        .text-white, .text-white-50 { color: #000 !important; }
        .badge { border: 1px solid #000 !important; color: #000 !important; background: #fff !important; }
    }
    .print-only { display: none; }
</style>

<div class="row justify-content-center">
    <div class="col-md-11">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
            <div class="d-flex align-items-center gap-3">
                <a href="<?= BASE_URL ?>/admin/staff" class="btn btn-outline-secondary btn-sm rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <?php if ($user['profile_image']): ?>
                    <img src="<?= BASE_URL ?>/<?= $user['profile_image'] ?>" class="rounded-circle shadow-sm" style="width: 45px; height: 45px; object-fit: cover;">
                <?php else: ?>
                    <div class="avatar-circle bg-<?= $user['role'] === 'admin' ? 'dark' : 'primary' ?> text-white d-flex align-items-center justify-content-center fw-bold" style="width: 45px; height: 45px; border-radius: 50%;">
                        <?= strtoupper(substr($user['fullname'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <h1 class="h2 mb-0"><?= e($user['fullname']) ?> <span class="badge bg-<?= $user['role'] === 'admin' ? 'dark' : 'primary' ?> fs-6 align-middle ms-2"><?= ucfirst($user['role']) ?></span></h1>
            </div>
            <div class="d-flex align-items-center gap-2 no-print">
                <button onclick="window.print()" class="btn btn-primary d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined">print</span> Print Report
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
        <div class="mb-4">
            <h6 class="text-muted text-uppercase small fw-bold mb-3 d-flex align-items-center gap-2">
                <span class="material-symbols-outlined fs-5">calendar_today</span> Today's Performance
                <span class="badge bg-danger rounded-pill smaller px-2">LIVE</span>
            </h6>
            <!-- Row 1: Volume & Cash Flow -->
            <div class="row g-2 mb-2">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm p-3 bg-white h-100">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Sales Count</small>
                        <h4 class="mb-0 text-dark"><?= number_format($todayStats['count']) ?></h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm p-3 bg-white h-100">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Revenue</small>
                        <h4 class="mb-0 text-primary">₵<?= number_format($todayStats['revenue'], 2) ?></h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm p-3 bg-white h-100">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">In Hand (New Sales)</small>
                        <h4 class="mb-0 text-success">₵<?= number_format($todayStats['collected'], 2) ?></h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm p-3 bg-white h-100">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Debt Recovered</small>
                        <h4 class="mb-0 text-success">₵<?= number_format($todayStats['debt_collected'], 2) ?></h4>
                        <div class="mt-1" style="font-size: 0.6rem; color: #999;">From Past Invoices</div>
                    </div>
                </div>
            </div>
            
            <!-- Row 2: Profitability -->
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3 bg-white h-100">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Gross Profit</small>
                        <h4 class="mb-0 text-info">₵<?= number_format($todayStats['profit'], 2) ?></h4>
                        <div class="mt-1" style="font-size: 0.6rem; color: #999;">Revenue - Cost Price</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3 bg-white h-100">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Expenses</small>
                        <h4 class="mb-0 text-danger">₵<?= number_format($todayStats['expenses'], 2) ?></h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3 bg-dark text-white h-100">
                        <small class="text-white-50 text-uppercase fw-bold" style="font-size: 0.65rem;">Net Today</small>
                        <h4 class="mb-0 <?= $todayStats['net'] < 0 ? 'text-danger' : 'text-info' ?>">₵<?= number_format($todayStats['net'], 2) ?></h4>
                        <div class="mt-1" style="font-size: 0.6rem; color: #aaa;">Gross Profit - Expenses</div>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="text-muted text-uppercase small fw-bold mb-3 d-flex align-items-center gap-2">
            <span class="material-symbols-outlined fs-5">monitoring</span> Account Lifetime stats
        </h6>
        <!-- KPI Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-primary text-white p-3">
                    <h6 class="text-white-50 text-uppercase small fw-bold">Lifetime Revenue</h6>
                    <h3 class="mb-0">₵<?= number_format($totalRevenue, 2) ?></h3>
                    <div class="mt-2 small text-white-50">Total Invoices Generated</div>
                    <div class="small text-white-50" style="font-size: 0.7rem;"><?= $stats['count'] ?> Completed Sales</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-success text-white p-3">
                    <h6 class="text-white-50 text-uppercase small fw-bold">Actual Collected</h6>
                    <h3 class="mb-0">₵<?= number_format($totalCollected, 2) ?></h3>
                    <div class="mt-2 small text-white-50"><?= $totalRevenue > 0 ? number_format(($totalCollected/$totalRevenue)*100, 1) : 0 ?>% Collection Rate</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-info text-white p-3">
                    <h6 class="text-white-50 text-uppercase small fw-bold">Total Gross Profit</h6>
                    <h3 class="mb-0">₵<?= number_format($totalProfit, 2) ?></h3>
                    <div class="mt-1 small text-white-50">Revenue - Cost Price</div>
                    <div class="mt-1 small text-white-50"><?= $totalRevenue > 0 ? number_format(($totalProfit/$totalRevenue)*100, 1) : 0 ?>% Gross Margin</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-dark text-white p-3">
                    <h6 class="text-white-50 text-uppercase small fw-bold">Net Contribution</h6>
                    <h3 class="mb-0 <?= $netContribution < 0 ? 'text-danger' : 'text-info' ?>">₵<?= number_format($netContribution, 2) ?></h3>
                    <div class="mt-2 small text-white-50">Profit minus Expenses</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Sales and Expenses -->
            <div class="col-lg-8">
                <!-- Recent Sales -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><span class="material-symbols-outlined align-middle me-1">shopping_cart</span> Recent Sales Activity</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Date</th>
                                        <th>Status</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-end pe-3 no-print">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentSales as $sale): 
                                        $isToday = date('Y-m-d', strtotime($sale['created_at'])) === date('Y-m-d');
                                    ?>
                                    <tr class="<?= $isToday ? 'table-warning bg-opacity-10' : '' ?>" <?= $isToday ? 'title="Transaction from Today"' : '' ?>>
                                        <td class="ps-3">
                                            <?= date('M j, Y H:i', strtotime($sale['created_at'])) ?>
                                            <?php if ($isToday): ?>
                                                <span class="badge bg-danger rounded-pill ms-1" style="font-size: 0.6rem;">TODAY</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill bg-<?= $sale['payment_status'] === 'paid' ? 'success' : ($sale['payment_status'] === 'partial' ? 'warning' : 'danger') ?> bg-opacity-10 text-<?= $sale['payment_status'] === 'paid' ? 'success' : ($sale['payment_status'] === 'partial' ? 'warning' : 'danger') ?>">
                                                <?= ucfirst($sale['payment_status']) ?>
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold">₵<?= number_format($sale['total_amount'], 2) ?></td>
                                        <td class="text-end pe-3 no-print">
                                            <a href="<?= BASE_URL ?>/sales/view?id=<?= $sale['id'] ?>" class="btn btn-sm btn-outline-secondary">View Invoice</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($recentSales)): ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted small">No recent sales found</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Expenses -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><span class="material-symbols-outlined align-middle me-1">payments</span> Recorded Expenses</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Date</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th class="text-end pe-3">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->prepare("SELECT * FROM expenditures WHERE recorded_by = ? AND is_deleted = 0 ORDER BY date DESC LIMIT 10");
                                    $stmt->execute([$uid]);
                                    $recentExpenses = $stmt->fetchAll();
                                    foreach ($recentExpenses as $exp): ?>
                                    <tr>
                                        <td class="ps-3"><?= date('M j, Y', strtotime($exp['date'])) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= e($exp['category']) ?></span></td>
                                        <td class="small"><?= e($exp['description']) ?></td>
                                        <td class="text-end text-danger fw-bold pe-3">₵<?= number_format($exp['amount'], 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($recentExpenses)): ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted small">No expenses recorded</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Login History and Info -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4 no-print">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><span class="material-symbols-outlined align-middle me-1">login</span> Login History</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php foreach ($loginHistory as $log): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-bold"><?= date('M j, Y', strtotime($log['login_at'])) ?></span>
                                    <span class="text-muted" style="font-size: 0.75rem;"><?= date('H:i', strtotime($log['login_at'])) ?></span>
                                </div>
                                <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                                    <span class="material-symbols-outlined fs-6" style="font-size: 14px;">language</span>
                                    IP: <?= e($log['ip_address']) ?>
                                </div>
                                <div class="small text-muted text-truncate" style="font-size: 0.7rem;" title="<?= e($log['user_agent']) ?>">
                                    <?= e(substr($log['user_agent'], 0, 50)) ?>...
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($loginHistory)): ?>
                            <div class="list-group-item text-center py-4 text-muted small">No login history recorded</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-0 py-2 text-center">
                        <a href="#" class="small text-decoration-none">View All History</a>
                    </div>
                </div>

                <div class="card shadow-sm border-0 bg-light no-print">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 small text-uppercase">Account Security</h6>
                        <div class="d-grid">
                            <form action="<?= BASE_URL ?>/users/update-role" method="POST" class="mb-2">
                                <input type="hidden" name="user_id" value="<?= $uid ?>">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <div class="input-group input-group-sm">
                                    <select name="role" class="form-select" <?= $uid == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                        <option value="sales" <?= $user['role'] === 'sales' ? 'selected' : '' ?>>Sales Role</option>
                                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin Role</option>
                                    </select>
                                    <button class="btn btn-outline-secondary" type="submit" <?= $uid == $_SESSION['user_id'] ? 'disabled' : '' ?>>Update</button>
                                </div>
                                <?php if ($uid == $_SESSION['user_id']): ?>
                                <small class="text-danger mt-1 d-block" style="font-size: 0.65rem;">You cannot change your own role.</small>
                                <?php endif; ?>
                            </form>
                            <a href="<?= BASE_URL ?>/users/edit?id=<?= $uid ?>" class="btn btn-sm btn-outline-primary">Edit User Profile</a>
                        </div>
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
