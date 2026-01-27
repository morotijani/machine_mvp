<?php
    $title = "Dashboard";
    ob_start();

    $isAdmin = ($_SESSION['role'] === 'admin');
?>
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
            <h1 class="h2">Dashboard</h1>
        </div>

        <div class="row mt-4">
            <!-- DAILY STATS SECTION -->
            <div class="col-12 mb-2">
                <h5 class="fw-bold text-muted d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined">today</span> Today's Performance (Actual Collections)
                </h5>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card p-3 h-100 bg-primary-subtle border-0 shadow-sm">
                    <h6 class="text-muted text-uppercase small fw-bold mb-2">Cash Collected Today</h6>
                    <h2 class="text-primary mb-0">₵<?php echo number_format($todayCollected, 2); ?></h2>
                    <small class="text-muted">From today's sales only</small>
                </div>
            </div>
            <?php if ($isAdmin): ?>
            <div class="col-md-4 mb-4">
                <div class="card p-3 h-100 bg-success-subtle border-0 shadow-sm">
                    <h6 class="text-muted text-uppercase small fw-bold mb-2">Realized Gross Profit</h6>
                    <h2 class="text-success mb-0">₵<?php echo number_format($todayRealizedProfit, 2); ?></h2>
                    <small class="text-muted">Proportional to collection</small>
                    <small class="text-muted" style="font-size: 10px;">Formula: (Collected / Total Sales) * Potential Profit</small>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card p-3 h-100 bg-dark border-0 shadow-sm text-white">
                    <h6 class="text-white-50 text-uppercase small fw-bold mb-2">Realized Net Profit</h6>
                    <h2 class="text-info mb-0">₵<?php echo number_format($todayRealizedNetProfit, 2); ?></h2>
                    <small class="text-white-50" style="font-size: 10px;">Formula: Realized Gross Profit - Today's Expenditures</small>
                </div>
            </div>
            <?php endif; ?>

            <!-- INVOICED STATS (FOR REFERENCE) -->
            <div class="col-12 mb-2">
                 <p class="text-muted small mb-1">
                     <span class="material-symbols-outlined align-middle fs-6">info</span> 
                     <strong>Invoiced Statistics:</strong> Totals based on today's invoices (Invoiced vs Expenses).
                 </p>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card p-2 border shadow-sm bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-bold">Daily Sales (Invoiced)</span>
                        <span class="fw-bold">₵<?php echo number_format($dailySales, 2); ?></span>
                    </div>
                </div>
            </div>
            <?php if ($isAdmin): ?>
            <div class="col-md-3 mb-3">
                <div class="card p-2 border shadow-sm bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-bold">Potential Profit</span>
                        <span class="fw-bold text-success">₵<?php echo number_format($dailyProfit, 2); ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <div class="col-md-3 mb-3">
                <div class="card p-2 border shadow-sm bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-bold">Expenditure</span>
                        <span class="fw-bold text-danger">₵<?php echo number_format($dailyExpenditures, 2); ?></span>
                    </div>
                </div>
            </div>
            <?php if ($isAdmin): ?>
            <div class="col-md-3 mb-3">
                <div class="card p-2 border shadow-sm bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-bold">Daily Net Profit</span>
                        <span class="fw-bold text-danger">₵<?php echo number_format($dailyNetProfit, 2); ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <!-- Expanded Invoiced Section -->
            <?php if ($isAdmin): ?>
            <!-- <div class="col-md-6 mb-4">
                <div class="card p-2 border-start border-4 border-info shadow-sm bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-bold d-block">Daily Net Profit (Invoiced)</span>
                            <small class="text-muted" style="font-size: 10px;">Formula: Potential Profit - Expenditure</small>
                        </div>
                        <h4 class="mb-0 text-info">₵<?php echo number_format($dailyNetProfit, 2); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card p-2 border-start border-4 border-success shadow-sm bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-bold d-block">Realized Gross Profit (Collected)</span>
                            <small class="text-muted" style="font-size: 10px;">Formula: (Collected / Total Sales) * Potential Profit</small>
                        </div>
                        <h4 class="mb-0 text-success">₵<?php echo number_format($todayRealizedProfit, 2); ?></h4>
                    </div>
                </div>
            </div> -->
            <?php endif; ?>

            <!-- LIFETIME & FINANCIAL SECTION -->
            <div class="col-12 mt-2 mb-3">
                <h5 class="fw-bold text-muted d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined">account_balance_wallet</span> Lifetime & Financial Summary
                </h5>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card p-3 h-100 border-0 shadow-sm" style="background-color: #e3f2fd;">
                    <h6 class="text-muted text-uppercase small fw-bold mb-2">Total Lifetime Sales</h6>
                    <h2 class="text-primary mb-0">₵<?php echo number_format($lifetimeStats['total'], 2); ?></h2>
                    <small class="text-muted mt-1">Total Revenue Generated</small>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card p-3 h-100 border-0 shadow-sm" style="background-color: #e8f5e9;">
                    <h6 class="text-muted text-uppercase small fw-bold mb-2">Cash Collected</h6>
                    <h2 class="text-success mb-0">₵<?php echo number_format($lifetimeStats['collected'], 2); ?></h2>
                    <small class="text-muted mt-1">Total payments received</small>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card p-3 h-100 border-0 shadow-sm" style="background-color: #fff3e0;">
                    <h6 class="text-muted text-uppercase small fw-bold mb-2">Balance Pending</h6>
                    <h2 class="text-warning mb-0">₵<?php echo number_format($lifetimeStats['total'] - $lifetimeStats['collected'], 2); ?></h2>
                    <small class="text-muted mt-1">Outstanding receivables</small>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card p-3 h-100 border-0 shadow-sm" style="background-color: #ffebee;">
                    <h6 class="text-muted text-uppercase small fw-bold mb-2">Total Outstanding Debt</h6>
                    <h2 class="text-danger mb-0">₵<?php echo number_format($totalDebt, 2); ?></h2>
                    <small class="text-muted mt-1">Sales + Standalone Debt</small>
                </div>
            </div>

            <!-- INVENTORY & ADDITIONAL SECTION -->
            <div class="col-12 mt-2 mb-3">
                <h5 class="fw-bold text-muted d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined">inventory_2</span> Inventory & Monthly Info
                </h5>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card p-3 h-100 border-0 shadow-sm bg-light">
                    <h6 class="text-muted text-uppercase small fw-bold mb-2">Low Stock Items</h6>
                    <h2 class="text-dark mb-0"><?php echo $lowStockCount; ?></h2>
                    <small class="text-muted mt-1">Quantity <= 5</small>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card p-3 h-100 border-0 shadow-sm bg-light">
                    <h6 class="text-muted text-uppercase small fw-bold mb-2">Total Sales Count</h6>
                    <h2 class="text-dark mb-0"><?php echo number_format($lifetimeStats['count']); ?></h2>
                    <small class="text-muted mt-1">Lifetime Transactions</small>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card p-3 h-100 border-0 shadow-sm bg-light text-danger">
                    <h6 class="text-danger-emphasis text-uppercase small fw-bold mb-2">Monthly Expenditure</h6>
                    <h2 class="mb-0">₵<?php echo number_format($monthlyExpenditures, 2); ?></h2>
                    <small class="text-muted mt-1"><?php echo date('F'); ?> expenses</small>
                </div>
            </div>
            <?php if ($isAdmin): ?>
            <div class="col-md-3 mb-4">
                <div class="card p-3 h-100 border-0 shadow-sm bg-dark text-white">
                    <h6 class="text-white-50 text-uppercase small fw-bold mb-2">Inventory Net Worth</h6>
                    <h3 class="mb-0">₵<?php echo number_format($inventoryWorth, 2); ?> <small class="fs-6 text-white-50">(Retail)</small></h3>
                    <h5 class="mb-0 text-info">₵<?php echo number_format($inventoryCost, 2); ?> <small class="fs-6 text-info-emphasis">(Cost)</small></h5>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-header bg-white fw-bold d-flex align-items-center gap-2 border-0 pt-3">
                        <span class="material-symbols-outlined text-muted">calendar_month</span>
                        Monthly Overview (<?php echo date('F Y'); ?>)
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                Monthly Sales Count
                                <span class="badge bg-secondary rounded-pill"><?php echo $monthlyStats['count']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                Monthly Revenue
                                <span class="fw-bold">₵<?php echo number_format($monthlyStats['total'], 2); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                Monthly Cash Collected
                                <span class="fw-bold text-success">₵<?php echo number_format($monthlyStats['collected'], 2); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 pb-0">
                                Monthly Balance Pending
                                <span class="fw-bold text-danger">₵<?php echo number_format($monthlyStats['total'] - $monthlyStats['collected'], 2); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-header bg-white fw-bold d-flex align-items-center gap-2 border-0 pt-3">
                        <span class="material-symbols-outlined text-muted">bolt</span>
                        Quick Actions
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-3">
                            <a href="<?= BASE_URL ?>/sales/create" class="btn btn-outline-primary btn-lg text-start d-flex align-items-center shadow-sm">
                                <span class="material-symbols-outlined me-2">shopping_cart_checkout</span> New Sale
                            </a>
                            <a href="<?= BASE_URL ?>/items/create" class="btn btn-outline-secondary btn-lg text-start d-flex align-items-center shadow-sm">
                                <span class="material-symbols-outlined me-2">add_box</span> Add New Item
                            </a>
                            <a href="<?= BASE_URL ?>/customers" class="btn btn-outline-success btn-lg text-start d-flex align-items-center shadow-sm">
                                <span class="material-symbols-outlined me-2">person_add</span> Manage Customers
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $content = ob_get_clean();
    require __DIR__ . '/../layouts/main.php';
?>