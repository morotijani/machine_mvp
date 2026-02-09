<?php
$title = "Reports";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
            <h1 class="h2">Reports & Analytics</h1>
            <div class="btn-toolbar mb-2 mb-md-0 gap-2">
                <a href="<?= BASE_URL ?>/reports/export?type=monthly_comparison&year=<?= $selectedYear ?>" class="btn btn-sm btn-outline-success d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size: 18px;">download</span> Export CSV
                </a>
                <form action="<?= BASE_URL ?>/reports" method="GET" class="d-flex align-items-center">
                    <label class="me-2 fw-bold text-muted">Year:</label>
                    <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                        <?php foreach ($availableYears as $year): ?>
                            <option value="<?php echo $year; ?>" <?php echo ($selectedYear == $year) ? 'selected' : ''; ?>>
                                <?php echo $year; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Monthly Sales Overview (<?php echo $selectedYear; ?>)</h5>
                <span class="badge bg-info bg-opacity-10 text-info">Excludes Voided Sales</span>
            </div>
            <div class="card-body">
                <canvas id="salesChart" width="400" height="150"></canvas>
            </div>
        </div>

        <!-- Advanced KPIs Row -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-start border-primary border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1 text-uppercase small fw-bold">Customer Retention Rate</h6>
                                <h3 class="mb-0 fw-bold"><?= number_format($retentionRate, 1) ?>%</h3>
                                <p class="text-muted small mb-0 mt-2">Percentage of customers with >1 purchase.</p>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                                <span class="material-symbols-outlined fs-1">group_add</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-start border-success border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1 text-uppercase small fw-bold">Inventory Turnover Ratio</h6>
                                <h3 class="mb-0 fw-bold"><?= number_format($inventoryTurnover, 2) ?>x</h3>
                                <p class="text-muted small mb-0 mt-2">Frequency of inventory stock replacement.</p>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                                <span class="material-symbols-outlined fs-1">inventory_2</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Selling Items Row -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Top 5 Items (By Volume)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle">
                                <thead>
                                    <tr class="text-muted">
                                        <th>Item</th>
                                        <th class="text-center">Qty Sold</th>
                                        <th class="text-end">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topSellingItems as $top): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?= e($top['name']) ?></div>
                                            <small class="text-muted"><?= e($top['sku']) ?></small>
                                        </td>
                                        <td class="text-center"><span class="badge bg-info bg-opacity-10 text-info"><?= number_format($top['total_qty']) ?></span></td>
                                        <td class="text-end fw-bold">₵<?= number_format($top['total_revenue'], 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Top 5 Items (By Revenue)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle">
                                <thead>
                                    <tr class="text-muted">
                                        <th>Item</th>
                                        <th class="text-end">Total Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topRevenueItems as $top): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?= e($top['name']) ?></div>
                                            <small class="text-muted"><?= e($top['sku']) ?></small>
                                        </td>
                                        <td class="text-end fw-bold text-success">₵<?= number_format($top['total_revenue'], 2) ?></td>
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
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Yearly Comparison (<?php echo $selectedYear; ?> vs <?php echo $lastYear; ?>)</h5>
                <span class="badge bg-info bg-opacity-10 text-info">Excludes Voided Sales</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Month</th>
                                <th class="text-end text-muted"><?php echo $lastYear; ?> Sales</th>
                                <th class="text-end"><?php echo $selectedYear; ?> Sales</th>
                                <th class="text-end">Profit (<?php echo $selectedYear; ?>)</th>
                                <th class="text-end">Difference</th>
                                <th class="text-end">Growth / Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($comparisonData as $monthNum => $data): 
                                $isCurrentMonth = ($selectedYear == date('Y') && $monthNum == date('n'));
                            ?>
                            <tr class="<?= $isCurrentMonth ? 'table-info fw-bold' : '' ?>">
                                <td><?= $data['month_name'] ?></td>
                                <td class="text-end text-muted">₵<?= number_format($data['last_year'], 2) ?></td>
                                <td class="text-end fw-bold">₵<?= number_format($data['current_year'], 2) ?></td>
                                <td class="text-end text-success">₵<?= number_format($data['current_profit'], 2) ?></td>
                                
                                <?php if ($data['difference'] > 0): ?>
                                    <td class="text-end text-success">+₵<?= number_format($data['difference'], 2) ?></td>
                                    <td class="text-end text-success">
                                        <div class="small"><span class="material-symbols-outlined align-middle fs-6">trending_up</span> <?= number_format($data['growth'], 1) ?>% Growth</div>
                                        <div class="small text-muted"><?= number_format($data['profit_margin'], 1) ?>% Margin</div>
                                    </td>
                                <?php elseif ($data['difference'] < 0): ?>
                                    <td class="text-end text-danger">-₵<?= number_format(abs($data['difference']), 2) ?></td>
                                    <td class="text-end text-danger">
                                        <div class="small"><span class="material-symbols-outlined align-middle fs-6">trending_down</span> <?= number_format($data['growth'], 1) ?>% Growth</div>
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
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td>Total</td>
                                <td class="text-end text-muted">₵<?php echo number_format(array_sum(array_column($comparisonData, 'last_year')), 2); ?></td>
                                <td class="text-end">₵<?php echo number_format(array_sum(array_column($comparisonData, 'current_year')), 2); ?></td>
                                <td class="text-end text-success">₵<?php echo number_format(array_sum(array_column($comparisonData, 'current_profit')), 2); ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Daily Sales (Legacy) -->
        <div class="card shadow-sm mb-4 collapsed-card"> <!-- Optional: Make this collapsible or just standard -->
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Daily Sales Log</h5>
                <small class="text-muted">Last 30 Days</small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
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
                                <td><?php echo date('M j, Y', strtotime($report['sale_date'])); ?></td>
                                <td class="text-center"><?php echo $report['count']; ?></td>
                                <td class="text-end fw-bold">₵<?php echo number_format($report['total'], 2); ?></td>
                                <td class="text-end fw-bold text-primary">₵<?php echo number_format($report['remaining_inventory_value'] ?? 0, 2); ?></td>
                                <td class="text-end text-success <?php echo $netDaily < 0 ? 'text-danger' : ''; ?>">
                                    ₵<?php echo number_format($report['profit'], 2); ?>
                                    <?php if ($report['total_expenditure'] > 0): ?>
                                        <div class="small text-muted" style="font-size: 0.7rem;">Net: ₵<?= number_format($netDaily, 2) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end text-danger">
                                    <?php if ($report['total_expenditure'] > 0): ?>
                                        -₵<?php echo number_format($report['total_expenditure'], 2); ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <span class="badge <?php echo $margin > 20 ? 'bg-success' : ($margin > 10 ? 'bg-warning' : 'bg-danger'); ?> bg-opacity-10 <?php echo $margin > 20 ? 'text-success' : ($margin > 10 ? 'text-warning' : 'text-danger'); ?>">
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
                backgroundColor: 'rgba(26, 115, 232, 0.7)', // Google Blue
                borderColor: 'rgba(26, 115, 232, 1)',
                borderWidth: 1,
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
                    position: 'top'
                },
                tooltip: {
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
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
