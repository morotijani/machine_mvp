<?php
    $title = "Dashboard";
    ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
            <h1 class="h2">Dashboard</h1>
            <!-- <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                    <span data-feather="calendar"></span>
                    This Week
                </button>
            </div> -->
        </div>

        <div class="row mt-4">
            <!-- Stat Cards -->
            <div class="col-md-4 mb-4">
                <div class="card p-3 h-100 bg-primary-subtle border-0">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted text-uppercase small fw-bold mb-0">Daily Sales</h6>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill d-flex align-items-center gap-1">
                            <span class="material-symbols-outlined" style="font-size: 14px;">calendar_today</span>
                            <?php echo date('M d'); ?>
                        </span>
                    </div>
                    <h2 class="text-primary mb-0">₵<?php echo number_format($dailySales, 2); ?></h2>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card p-3 h-100 bg-danger-subtle border-0">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted text-uppercase small fw-bold mb-0">Total Outstanding Debt</h6>
                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">Action Needed</span>
                    </div>
                    <h2 class="text-danger mb-0">₵<?php echo number_format($totalDebt, 2); ?></h2>
                    <small class="text-muted mt-2"><a href="<?= BASE_URL ?>/customers" class="text-decoration-none d-flex align-items-center gap-1">View Debtors <span class="material-symbols-outlined" style="font-size: 16px;">arrow_forward</span></a></small>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card p-3 h-100 bg-warning-subtle border-0">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted text-uppercase small fw-bold mb-0">Low Stock Items</h6>
                        <span class="badge bg-warning bg-opacity-10 text-dark rounded-pill">Alert</span>
                    </div>
                    <h2 class="text-dark mb-0"><?php echo $lowStockCount; ?></h2>
                    <small class="text-muted mt-2"><a href="<?= BASE_URL ?>/items" class="text-decoration-none d-flex align-items-center gap-1">Check Inventory <span class="material-symbols-outlined" style="font-size: 16px;">arrow_forward</span></a></small>
                </div>
            </div>
        </div>

        <?php if ($_SESSION['role'] === 'admin'): ?>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card p-3 h-100 border-0 shadow-sm bg-success bg-opacity-10">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted text-uppercase small fw-bold mb-0">Total Inventory Net Worth</h6>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill">Asset Value</span>
                    </div>
                    <h2 class="text-success mb-0">₵<?php echo number_format($inventoryWorth, 2); ?></h2>
                    <small class="text-muted mt-1">Based on current stock & price</small>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card p-3 h-100 border-0 shadow-sm bg-info bg-opacity-10">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted text-uppercase small fw-bold mb-0">Total Lifetime Sales</h6>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill">Revenue</span>
                    </div>
                    <h2 class="text-info mb-0">₵<?php echo number_format($totalSoldWorth, 2); ?></h2>
                    <small class="text-muted mt-1">Gross total of non-voided sales</small>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white fw-bold d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined text-muted">calendar_month</span>
                        Monthly Overview (<?php echo date('F Y'); ?>)
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Total Sales Count
                                <span class="badge bg-secondary rounded-pill"><?php echo $monthlyStats['count']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Total Revenue Generated
                                <span class="fw-bold">₵<?php echo number_format($monthlyStats['total'], 2); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Cash Collected
                                <span class="fw-bold text-success">₵<?php echo number_format($monthlyStats['collected'], 2); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Balance Pending
                                <span class="fw-bold text-danger">₵<?php echo number_format($monthlyStats['total'] - $monthlyStats['collected'], 2); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white fw-bold d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined text-muted">bolt</span>
                        Quick Actions
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-3">
                            <a href="<?= BASE_URL ?>/sales/create" class="btn btn-outline-primary btn-lg text-start d-flex align-items-center">
                                <span class="material-symbols-outlined me-2">shopping_cart_checkout</span> New Sale
                            </a>
                            <a href="<?= BASE_URL ?>/items/create" class="btn btn-outline-secondary btn-lg text-start d-flex align-items-center">
                                <span class="material-symbols-outlined me-2">add_box</span> Add New Item
                            </a>
                            <a href="<?= BASE_URL ?>/customers" class="btn btn-outline-success btn-lg text-start d-flex align-items-center">
                                <span class="material-symbols-outlined me-2">person_add</span> Manage Customers
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php
    $content = ob_get_clean();
    require __DIR__ . '/../layouts/main.php';
?>
