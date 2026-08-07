<?php
$title = "Reports";
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
    .google-table-card tfoot td {
        background-color: #f8f9fa;
        padding: 16px 32px;
        color: #1f1f1f;
        border-top: 1px solid #e3e3e3;
        border-bottom: none;
    }
    .google-stat-card {
        background: #fff;
        border-radius: 24px;
        padding: 24px 32px;
        border: none;
        box-shadow: none;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .google-stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .google-stat-icon .material-symbols-outlined {
        font-size: 28px;
    }
    .google-select-wrap {
        position: relative;
        display: inline-flex;
        align-items: center;
    }
    .google-select {
        appearance: none;
        background-color: #f8f9fa;
        border: 1px solid transparent;
        border-radius: 24px;
        padding: 8px 40px 8px 20px;
        font-size: 14px;
        color: #1f1f1f;
        font-weight: 500;
        cursor: pointer;
        outline: none;
        transition: all 0.2s;
    }
    .google-select:hover {
        background-color: #f1f3f4;
    }
    .google-select:focus {
        border-color: #0b57d0;
        background-color: #fff;
    }
    .google-select-wrap .material-symbols-outlined {
        position: absolute;
        right: 12px;
        pointer-events: none;
        color: #444746;
        font-size: 20px;
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
</style>

<div class="row justify-content-center">
    <div class="col-12 col-xxl-11">
        <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-4 gap-2">
            <h1 class="h3 mb-0" style="color: #1f1f1f; font-weight: 400;">Reports & Analytics</h1>
            <div class="d-flex flex-wrap gap-3 align-items-center justify-content-start justify-content-md-end flex-grow-1">
                
                <form action="<?= BASE_URL ?>/reports" method="GET" class="m-0">
                    <div class="google-select-wrap">
                        <select name="year" class="google-select" onchange="this.form.submit()">
                            <?php foreach ($availableYears as $year): ?>
                                <option value="<?php echo $year; ?>" <?php echo ($selectedYear == $year) ? 'selected' : ''; ?>>
                                    <?php echo $year; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="material-symbols-outlined">arrow_drop_down</span>
                    </div>
                </form>

                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= BASE_URL ?>/reports/export?type=monthly_comparison&year=<?= $selectedYear ?>" class="google-btn google-btn-outline">
                        <span class="material-symbols-outlined" style="font-size: 18px;">download</span> Export CSV
                    </a>
                    <a href="<?= BASE_URL ?>/reports/daily" class="google-btn google-btn-primary">
                        <span class="material-symbols-outlined" style="font-size: 18px;">calendar_today</span> Daily Activity
                    </a>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="google-table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Monthly Sales Overview (<?php echo $selectedYear; ?>)</h5>
                <span class="google-pill google-pill-info">Excludes Voided</span>
            </div>
            <div class="card-body p-4">
                <canvas id="salesChart" width="400" height="150"></canvas>
            </div>
        </div>

        <!-- Advanced KPIs Row -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="google-stat-card border-start border-4" style="border-color: #0b57d0 !important;">
                    <div>
                        <h6 class="text-muted mb-1 text-uppercase small" style="letter-spacing: 0.5px; font-weight: 500;">Customer Retention Rate</h6>
                        <h3 class="mb-0 fw-normal" style="color: #1f1f1f; font-size: 28px;"><?= number_format($retentionRate, 1) ?>%</h3>
                        <p class="text-muted small mb-0 mt-2">Percentage of customers with >1 purchase.</p>
                    </div>
                    <div class="google-stat-icon" style="background-color: #e8f0fe; color: #0b57d0;">
                        <span class="material-symbols-outlined">group_add</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="google-stat-card border-start border-4" style="border-color: #188038 !important;">
                    <div>
                        <h6 class="text-muted mb-1 text-uppercase small" style="letter-spacing: 0.5px; font-weight: 500;">Inventory Turnover Ratio</h6>
                        <h3 class="mb-0 fw-normal" style="color: #1f1f1f; font-size: 28px;"><?= number_format($inventoryTurnover, 2) ?>x</h3>
                        <p class="text-muted small mb-0 mt-2">Frequency of inventory stock replacement.</p>
                    </div>
                    <div class="google-stat-icon" style="background-color: #e6f4ea; color: #188038;">
                        <span class="material-symbols-outlined">inventory_2</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Selling Items Row -->
        <div class="row mb-4">
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="google-table-card h-100 mb-0">
                    <div class="card-header">
                        <h5 class="mb-0">Top 5 Items (By Volume)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Qty Sold</th>
                                        <th class="text-end">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topSellingItems as $top): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 500; color: #1f1f1f;"><?= e($top['name']) ?></div>
                                            <small class="text-muted"><?= e($top['sku']) ?></small>
                                        </td>
                                        <td class="text-center"><span class="google-pill google-pill-info"><?= number_format($top['total_qty']) ?></span></td>
                                        <td class="text-end fw-medium" style="color: #1f1f1f;">₵<?= format_large_number($top['total_revenue']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="google-table-card h-100 mb-0">
                    <div class="card-header">
                        <h5 class="mb-0">Top 5 Items (By Revenue)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-end">Total Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topRevenueItems as $top): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 500; color: #1f1f1f;"><?= e($top['name']) ?></div>
                                            <small class="text-muted"><?= e($top['sku']) ?></small>
                                        </td>
                                        <td class="text-end fw-medium" style="color: #188038;">₵<?= format_large_number($top['total_revenue']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comparison Table -->
        <div class="google-table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Yearly Comparison (<?php echo $selectedYear; ?> vs <?php echo $lastYear; ?>)</h5>
                <span class="google-pill google-pill-info">Excludes Voided</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th class="text-end"><?php echo $lastYear; ?> Sales</th>
                                <th class="text-end"><?php echo $selectedYear; ?> Sales</th>
                                <th class="text-end">Gross Profit</th>
                                <th class="text-end">Expenses</th>
                                <th class="text-end">Net Profit</th>
                                <th class="text-end">Difference</th>
                                <th class="text-end">Growth / Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($comparisonData as $monthNum => $data): 
                                $isCurrentMonth = ($selectedYear == date('Y') && $monthNum == date('n'));
                            ?>
                            <tr <?= $isCurrentMonth ? 'style="--bs-table-bg: #e8f0fe; background-color: #e8f0fe;"' : '' ?>>
                                <td style="<?= $isCurrentMonth ? 'font-weight: 500;' : '' ?>"><?= $data['month_name'] ?></td>
                                <td class="text-end text-muted">₵<?= format_large_number($data['last_year']) ?></td>
                                <td class="text-end" style="color: #1f1f1f; font-weight: 500;">₵<?= format_large_number($data['current_year']) ?></td>
                                <td class="text-end text-muted">₵<?= format_large_number($data['current_profit']) ?></td>
                                <td class="text-end" style="color: #d93025;">₵<?= format_large_number($data['current_expenses']) ?></td>
                                <td class="text-end" style="color: #188038; font-weight: 500;">₵<?= format_large_number($data['final_profit']) ?></td>
                                
                                <?php if ($data['difference'] > 0): ?>
                                    <td class="text-end" style="color: #188038;">+₵<?= format_large_number($data['difference']) ?></td>
                                    <td class="text-end">
                                        <div class="small" style="color: #188038;"><span class="material-symbols-outlined align-middle" style="font-size: 16px;">trending_up</span> <?= number_format($data['growth'], 1) ?>% Growth</div>
                                        <div class="small text-muted"><?= number_format($data['profit_margin'], 1) ?>% Margin</div>
                                    </td>
                                <?php elseif ($data['difference'] < 0): ?>
                                    <td class="text-end" style="color: #d93025;">-₵<?= format_large_number(abs($data['difference'])) ?></td>
                                    <td class="text-end">
                                        <div class="small" style="color: #d93025;"><span class="material-symbols-outlined align-middle" style="font-size: 16px;">trending_down</span> <?= number_format($data['growth'], 1) ?>% Growth</div>
                                        <div class="small text-muted"><?= number_format($data['profit_margin'], 1) ?>% Margin</div>
                                    </td>
                                <?php else: ?>
                                    <td class="text-end text-muted">-</td>
                                    <td class="text-end text-muted">
                                        <div class="small text-muted"><?= number_format($data['profit_margin'], 1) ?>% Margin</div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td style="font-weight: 500;">Total</td>
                                <td class="text-end text-muted">₵<?php echo format_large_number(array_sum(array_column($comparisonData, 'last_year'))); ?></td>
                                <td class="text-end" style="color: #1f1f1f; font-weight: 500;">₵<?php echo format_large_number(array_sum(array_column($comparisonData, 'current_year'))); ?></td>
                                <td class="text-end text-muted">₵<?php echo format_large_number(array_sum(array_column($comparisonData, 'current_profit'))); ?></td>
                                <td class="text-end" style="color: #d93025;">₵<?php echo format_large_number(array_sum(array_column($comparisonData, 'current_expenses'))); ?></td>
                                <td class="text-end" style="color: #188038; font-weight: 500;">₵<?php echo format_large_number(array_sum(array_column($comparisonData, 'final_profit'))); ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Daily Sales (Legacy) -->
        <div class="google-table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Daily Sales Log</h5>
                <span class="text-muted" style="font-size: 13px;">Last 30 Days</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th class="text-center">Sales</th>
                                <th class="text-end">Total Amount</th>
                                <th class="text-end text-primary">Remaining Items Value</th>
                                <th class="text-end">Profit</th>
                                <th class="text-end">Expenditure</th>
                                <th class="text-end">Margin %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dailyReports as $report): 
                                $margin = ($report['total'] > 0) ? ($report['profit'] / $report['total']) * 100 : 0;
                                $netDaily = $report['profit'] - ($report['total_expenditure'] ?? 0);
                            ?>
                            <tr>
                                <td style="color: #1f1f1f;"><?php echo date('M j, Y', strtotime($report['sale_date'])); ?></td>
                                <td class="text-center"><span class="google-pill" style="background: #f1f3f4; color: #444746;"><?php echo $report['count']; ?></span></td>
                                <td class="text-end" style="color: #1f1f1f; font-weight: 500;">₵<?php echo format_large_number($report['total']); ?></td>
                                <td class="text-end" style="color: #0b57d0; font-weight: 500;">₵<?php echo format_large_number($report['remaining_inventory_value'] ?? 0); ?></td>
                                <td class="text-end" style="color: <?php echo $netDaily < 0 ? '#d93025' : '#188038'; ?>">
                                    ₵<?php echo format_large_number($report['profit']); ?>
                                    <?php if ($report['total_expenditure'] > 0): ?>
                                        <div class="small text-muted" style="font-size: 12px; margin-top: 2px;">Net: ₵<?= format_large_number($netDaily) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end" style="color: #d93025;">
                                    <?php if ($report['total_expenditure'] > 0): ?>
                                        -₵<?php echo format_large_number($report['total_expenditure']); ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <span class="google-pill" style="<?php 
                                        echo $margin > 20 ? 'background-color: #e6f4ea; color: #188038;' : 
                                            ($margin > 10 ? 'background-color: #fef7e0; color: #e37400;' : 'background-color: #fce8e6; color: #d93025;'); 
                                    ?>">
                                        <?php echo number_format($margin, 1); ?>%
                                    </span>
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

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    // PHP Data to JS
    const monthlyData = <?php echo json_encode(array_values($monthlySales)); ?>;
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    new Chart(ctx, {
        type: 'bar', // Mixed chart type could be used, but bar is good for volume
        data: {
            labels: months,
            datasets: [{
                label: 'Sales Revenue (₵) - <?php echo $selectedYear; ?>',
                data: monthlyData,
                backgroundColor: '#0b57d0', // Google Blue
                borderRadius: 4,
                barPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f3f4'
                    },
                    ticks: {
                        callback: function(value) {
                            return '₵' + value;
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            family: "'Inter', sans-serif",
                            size: 13
                        }
                    }
                },
                tooltip: {
                    backgroundColor: '#1f1f1f',
                    padding: 12,
                    titleFont: {
                        family: "'Inter', sans-serif",
                        size: 13
                    },
                    bodyFont: {
                        family: "'Inter', sans-serif",
                        size: 14,
                        weight: 'bold'
                    },
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('en-GH', { style: 'currency', currency: 'GHS' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
    
    // Enable tooltips everywhere (for format_large_number)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
