<?php
$title = "Dashboard";
ob_start();

$isAdmin = ($_SESSION['role'] === 'admin');
?>
<style>
    /* Google Dashboard specific styles */
    .dashboard-header {
        margin-bottom: 32px;
    }

    .dashboard-title {
        font-size: 28px;
        font-weight: 400;
        color: #202124;
        letter-spacing: -0.5px;
    }

    .widget-card {
        border-radius: 24px;
        border: none;
        padding: 24px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .widget-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .widget-icon-container {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
    }

    .widget-icon-container .material-symbols-outlined {
        font-size: 24px;
    }

    .widget-title {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 4px;
        color: #444746;
    }

    .widget-value {
        font-size: 32px;
        font-weight: 400;
        margin-bottom: 4px;
        letter-spacing: -0.5px;
        color: #1f1f1f;
    }

    .widget-subtitle {
        font-size: 12px;
        color: #5f6368;
    }

    /* Standard Google Card */
    .google-card {
        background: #fff;
        border-radius: 24px;
        border: 1px solid #dadce0;
        padding: 24px;
        margin-bottom: 24px;
    }

    .google-card-header {
        font-size: 18px;
        font-weight: 400;
        color: #202124;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .google-btn-action {
        border-radius: 20px;
        padding: 10px 20px;
        font-weight: 500;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid #dadce0;
        background: #fff;
        color: #1a73e8;
        transition: background-color 0.2s;
        text-decoration: none;
    }

    .google-btn-action:hover {
        background-color: #f8f9fa;
        color: #174ea6;
    }

    .table-google {
        width: 100%;
        border-collapse: collapse;
    }

    .table-google th {
        font-weight: 500;
        color: #5f6368;
        font-size: 13px;
        padding: 12px 16px;
        border-bottom: 1px solid #dadce0;
        text-align: left;
    }

    .table-google td {
        padding: 16px;
        border-bottom: 1px solid #f1f3f4;
        font-size: 14px;
        color: #202124;
        vertical-align: middle;
    }

    .table-google tr:last-child td {
        border-bottom: none;
    }

    .section-label {
        font-size: 14px;
        font-weight: 500;
        color: #5f6368;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 16px;
        margin-top: 32px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-11 col-xl-10">

        <!-- Header Section -->
        <div class="dashboard-header d-flex justify-content-between align-items-end pt-4">
            <div>
                <h1 class="dashboard-title">Welcome back, <?= e($_SESSION['username'] ?? 'User') ?></h1>
                <p class="text-muted mb-0" style="font-size: 14px;">Here's what's happening with your store today.</p>
            </div>
            <?php if (!empty($_SESSION['profile_image'])): ?>
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($_SESSION['profile_image']) ?>" class="rounded-circle border"
                    style="width: 56px; height: 56px; object-fit: cover;">
            <?php else: ?>
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                    style="width: 56px; height: 56px; font-weight: 500; font-size: 24px; background-color: #0b57d0; color: white;">
                    <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- TODAY'S PERFORMANCE -->
        <div class="section-label">
            <span class="material-symbols-outlined" style="font-size: 18px;">today</span>
            Today's Performance
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="widget-card" style="background-color: #e8f0fe;">
                    <div class="widget-icon-container" style="background-color: #d2e3fc; color: #1967d2;">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                    <div class="widget-title">Cash Collected Today</div>
                    <div class="widget-value text-primary">₵<?php echo format_large_number($todayNewSalesCollected); ?>
                    </div>
                    <div class="widget-subtitle">From sales, net of returns & deleted sales</div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="widget-card" style="background-color: #e6f4ea;">
                    <div class="widget-icon-container" style="background-color: #ceead6; color: #137333;">
                        <span class="material-symbols-outlined">assignment_return</span>
                    </div>
                    <div class="widget-title">Debt Recovered</div>
                    <div class="widget-value text-success">₵<?php echo format_large_number($todayDebtCollected); ?>
                    </div>
                    <div class="widget-subtitle">From past invoices</div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="widget-card" style="background-color: #fef7e0;">
                    <div class="widget-icon-container" style="background-color: #feefc3; color: #e37400;">
                        <span class="material-symbols-outlined">account_balance_wallet</span>
                    </div>
                    <div class="widget-title">Total Net Collections</div>
                    <div class="widget-value text-warning-emphasis">
                        ₵<?php echo format_large_number($totalNetCollections); ?></div>
                    <div class="widget-subtitle">New + Old - Refunds - Expenses</div>
                </div>
            </div>

            <?php if ($isAdmin): ?>
                <div class="col-md-6">
                    <div class="widget-card" style="background-color: #e4f7fb;">
                        <div class="widget-icon-container" style="background-color: #cbf0f8; color: #007b83;">
                            <span class="material-symbols-outlined">trending_up</span>
                        </div>
                        <div class="widget-title">Realized Gross Profit</div>
                        <div class="widget-value text-info-emphasis">
                            ₵<?php echo format_large_number($todayRealizedProfit); ?></div>
                        <div class="widget-subtitle">Earned from total cash collected above</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="widget-card" style="background-color: #202124;">
                        <div class="widget-icon-container" style="background-color: #3c4043; color: #e8eaed;">
                            <span class="material-symbols-outlined">monitoring</span>
                        </div>
                        <div class="widget-title" style="color: #9aa0a6;">Realized Net Profit</div>
                        <div class="widget-value" style="color: #e8eaed;">
                            ₵<?php echo format_large_number($todayRealizedNetProfit); ?></div>
                        <div class="widget-subtitle" style="color: #9aa0a6;">Realized GP - Expenditures</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- INVOICED STATS -->
        <div class="section-label">
            <span class="material-symbols-outlined" style="font-size: 18px;">receipt_long</span>
            Invoiced Statistics
        </div>

        <div class="google-card" style="padding: 0;">
            <div
                style="padding: 16px 24px; background-color: #f8f9fa; border-bottom: 1px solid #dadce0; border-radius: 24px 24px 0 0;">
                <p class="text-muted small mb-0 d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined fs-6">info</span>
                    Totals based on today's invoices (Invoiced vs Expenses).
                </p>
            </div>
            <div class="row g-0">
                <div class="col-md-3 border-end">
                    <div class="p-4 text-center">
                        <div class="text-muted small fw-medium mb-1">Daily Sales (Invoiced)</div>
                        <div class="fs-4 fw-normal" style="color: #202124;">
                            ₵<?php echo format_large_number($dailySales); ?></div>
                    </div>
                </div>
                <?php if ($isAdmin): ?>
                    <div class="col-md-3 border-end">
                        <div class="p-4 text-center">
                            <div class="text-muted small fw-medium mb-1">Potential Profit</div>
                            <div class="fs-4 fw-normal text-success">₵<?php echo format_large_number($dailyProfit); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="col-md-3 <?php echo $isAdmin ? 'border-end' : ''; ?>">
                    <div class="p-4 text-center">
                        <div class="text-muted small fw-medium mb-1">Expenditure</div>
                        <div class="fs-4 fw-normal text-danger">₵<?php echo format_large_number($dailyExpenditures); ?>
                        </div>
                    </div>
                </div>
                <?php if ($isAdmin): ?>
                    <div class="col-md-3">
                        <div class="p-4 text-center">
                            <div class="text-muted small fw-medium mb-1">Daily Net Profit</div>
                            <div class="fs-4 fw-normal text-danger">₵<?php echo format_large_number($dailyNetProfit); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($isAdmin): ?>
        <!-- LIFETIME & FINANCIAL SECTION -->
        <div class="section-label">
            <span class="material-symbols-outlined" style="font-size: 18px;">public</span>
            Lifetime Summary
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="widget-card border" style="background-color: #fff; padding: 20px;">
                    <div class="widget-title">Total Lifetime Sales</div>
                    <div class="widget-value fs-3 text-primary">
                        ₵<?php echo format_large_number($lifetimeStats['total']); ?></div>
                    <div class="widget-subtitle">Total Revenue Generated</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget-card border" style="background-color: #fff; padding: 20px;">
                    <div class="widget-title">Cash Collected</div>
                    <div class="widget-value fs-3 text-success">
                        ₵<?php echo format_large_number($lifetimeStats['collected']); ?></div>
                    <div class="widget-subtitle">Total payments received</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget-card border" style="background-color: #fff; padding: 20px;">
                    <div class="widget-title">Balance Pending</div>
                    <div class="widget-value fs-3 text-warning-emphasis">
                        ₵<?php echo format_large_number($lifetimeStats['total'] - $lifetimeStats['collected']); ?></div>
                    <div class="widget-subtitle">Outstanding receivables</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget-card border" style="background-color: #fff; padding: 20px;">
                    <div class="widget-title">Total Outstanding Debt</div>
                    <div class="widget-value fs-3 text-danger">₵<?php echo format_large_number($totalDebt); ?></div>
                    <div class="widget-subtitle">
                        <?= (!isset($settings['enable_debt_module']) || $settings['enable_debt_module'] == 1) ? 'Sales + Standalone Debt' : 'From unpaid sales invoices' ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- INVENTORY & ADDITIONAL SECTION -->
        <div class="section-label">
            <span class="material-symbols-outlined" style="font-size: 18px;">inventory_2</span>
            Inventory & Monthly Info
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="widget-card border" style="background-color: #f8f9fa; padding: 20px;">
                    <div class="widget-title">Low Stock Items</div>
                    <div class="widget-value fs-3" style="color: #202124;"><?php echo $lowStockCount; ?></div>
                    <div class="widget-subtitle">Quantity <= 5</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget-card border" style="background-color: #f8f9fa; padding: 20px;">
                        <div class="widget-title">Total Sales Count</div>
                        <div class="widget-value fs-3" style="color: #202124;">
                            <?php echo number_format($lifetimeStats['count']); ?>
                        </div>
                        <div class="widget-subtitle">Lifetime Transactions</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget-card border" style="background-color: #fcf1f1; padding: 20px;">
                        <div class="widget-title text-danger">Monthly Expenditure</div>
                        <div class="widget-value fs-3 text-danger">
                            ₵<?php echo format_large_number($monthlyExpenditures); ?></div>
                        <div class="widget-subtitle"><?php echo date('F'); ?> expenses</div>
                    </div>
                </div>
                <?php if ($isAdmin): ?>
                    <div class="col-md-3">
                        <div class="widget-card border" style="background-color: #f8f9fa; padding: 20px;">
                            <div class="widget-title">Inventory Net Worth</div>
                            <div class="widget-value fs-3 text-success">₵<?php echo format_large_number($inventoryWorth); ?>
                            </div>
                            <div class="widget-subtitle text-info">Cost: ₵<?php echo format_large_number($inventoryCost); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($isAdmin): ?>
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="google-card h-100">
                        <div class="google-card-header">
                            <span class="material-symbols-outlined text-primary">calendar_month</span>
                            Monthly Overview (<?php echo date('F Y', strtotime('last month')); ?>)
                        </div>
                        <table class="table-google mt-2">
                            <tbody>
                                <tr>
                                    <td style="padding-left: 0;">Monthly Sales Count</td>
                                    <td class="text-end" style="padding-right: 0;"><span
                                            class="badge rounded-pill bg-light text-dark border"><?php echo $lastMonthStats['count']; ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 0;">Monthly Revenue</td>
                                    <td class="text-end fw-medium" style="padding-right: 0; color: #202124;">
                                        ₵<?php echo format_large_number($lastMonthStats['total']); ?></td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 0;">Monthly Cash Collected</td>
                                    <td class="text-end fw-medium text-success" style="padding-right: 0;">
                                        ₵<?php echo format_large_number($lastMonthStats['collected']); ?></td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 0;">Monthly Balance Pending</td>
                                    <td class="text-end fw-medium text-danger" style="padding-right: 0;">
                                        ₵<?php echo format_large_number($lastMonthStats['total'] - $lastMonthStats['collected']); ?>
                                    </td>
                                </tr>
                                <?php if ($isAdmin): ?>
                                <tr>
                                    <td style="padding-left: 0; color: #174ea6;">Profit from Cash Collected</td>
                                    <td class="text-end fw-bold" style="padding-right: 0; color: #174ea6;">
                                        ₵<?php echo format_large_number($lastMonthStats['profit']); ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="google-card h-100" style="background-color: #f8fafd; border: 1px solid #d2e3fc;">
                        <div class="google-card-header" style="color: #174ea6;">
                            <span class="material-symbols-outlined" style="color: #1967d2;">calendar_month</span>
                            Monthly Overview (<?php echo date('F Y'); ?>)
                        </div>
                        <table class="table-google mt-2">
                            <tbody>
                                <tr>
                                    <td style="padding-left: 0; border-color: #e8f0fe;">Monthly Sales Count</td>
                                    <td class="text-end" style="padding-right: 0; border-color: #e8f0fe;"><span
                                            class="badge rounded-pill bg-white text-dark border"><?php echo $thisMonthStats['count']; ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 0; border-color: #e8f0fe;">Monthly Revenue</td>
                                    <td class="text-end fw-medium" style="padding-right: 0; color: #202124; border-color: #e8f0fe;">
                                        ₵<?php echo format_large_number($thisMonthStats['total']); ?></td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 0; border-color: #e8f0fe;">Monthly Cash Collected</td>
                                    <td class="text-end fw-medium text-success" style="padding-right: 0; border-color: #e8f0fe;">
                                        ₵<?php echo format_large_number($thisMonthStats['collected']); ?></td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 0; border-color: #e8f0fe;">Monthly Balance Pending</td>
                                    <td class="text-end fw-medium text-danger" style="padding-right: 0; border-color: #e8f0fe;">
                                        ₵<?php echo format_large_number($thisMonthStats['total'] - $thisMonthStats['collected']); ?>
                                    </td>
                                </tr>
                                <?php if ($isAdmin): ?>
                                <tr>
                                    <td style="padding-left: 0; color: #174ea6; border-color: transparent;">Profit from Cash Collected</td>
                                    <td class="text-end fw-bold" style="padding-right: 0; color: #174ea6; border-color: transparent;">
                                        ₵<?php echo format_large_number($thisMonthStats['profit']); ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- TODAY'S RETURNS TABLE -->
            <div class="google-card p-0 mb-5">
                <div class="d-flex justify-content-between align-items-center"
                    style="padding: 20px 24px; border-bottom: 1px solid #dadce0;">
                    <div class="google-card-header mb-0">
                        <span class="material-symbols-outlined text-danger">assignment_return</span>
                        Today's Returned Items
                    </div>
                    <span class="badge rounded-pill bg-danger-subtle text-danger"
                        style="font-size: 12px; font-weight: 500; padding: 6px 12px;">
                        <?php echo count($todayReturnedItemsList); ?> Items
                    </span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($todayReturnedItemsList)): ?>
                        <div class="text-center py-5 text-muted">
                            <span class="material-symbols-outlined mb-2"
                                style="font-size: 48px; color: #dadce0;">inventory_2</span>
                            <div style="font-size: 14px;">No items have been returned today.</div>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table-google">
                                <thead style="background-color: #f8f9fa;">
                                    <tr>
                                        <th style="padding-left: 24px;">Time</th>
                                        <th>Item Name</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Deduction</th>
                                        <?php if ($isAdmin): ?>
                                            <th style="padding-right: 24px;">Sales Person</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($todayReturnedItemsList as $ret): ?>
                                        <tr>
                                            <td class="text-muted" style="font-size: 13px; padding-left: 24px;">
                                                <?php echo $ret['return_time']; ?>
                                            </td>
                                            <td class="fw-medium"><?php echo htmlspecialchars($ret['item_name']); ?></td>
                                            <td class="text-center">
                                                <span
                                                    class="badge rounded-pill bg-light text-dark border"><?php echo $ret['quantity']; ?></span>
                                            </td>
                                            <td class="text-end fw-medium text-danger">
                                                ₵<?php echo number_format($ret['deduction'], 2); ?>
                                            </td>
                                            <?php if ($isAdmin): ?>
                                                <td style="padding-right: 24px;">
                                                    <span
                                                        class="badge bg-light text-primary border rounded-pill fw-normal"><?php echo htmlspecialchars($ret['salesperson']); ?></span>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
    <?php
    $content = ob_get_clean();
    require __DIR__ . '/../layouts/main.php';
    ?>