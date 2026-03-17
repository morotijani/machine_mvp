<?php
$title = "Daily Activity Report";
ob_start();
$displayDate = date('M j, Y', strtotime($date));
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
    <div>
        <h1 class="h2 mb-0">Daily Activity Report</h1>
        <p class="text-muted">Detailed performance and audit for <?= $displayDate ?></p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <form class="d-flex align-items-center gap-2 bg-white p-2 rounded shadow-sm">
            <label for="date" class="small fw-bold text-muted text-nowrap">Select Date:</label>
            <input type="date" name="date" id="date" class="form-control form-control-sm border-0" value="<?= $date ?>" onchange="this.form.submit()">
            <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                <span class="material-symbols-outlined fs-6">print</span> Print
            </button>
        </form>
    </div>
</div>

<!-- Performance Summary Cards -->
<div class="row g-3 mb-4">
    <!-- Total Net Collection -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 bg-primary text-white overflow-hidden" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
            <div class="card-body position-relative">
                <div class="position-absolute top-0 end-0 p-3 opacity-10">
                    <span class="material-symbols-outlined" style="font-size: 80px;">payments</span>
                </div>
                <h6 class="text-white-50 small text-uppercase fw-bold mb-1">Total Net Collection</h6>
                <h3 class="fw-bold mb-1">GH₵ <?= number_format($totalNetCollection, 2) ?></h3>
                <p class="small mb-0 text-white-50">Physical cash/digital entry today</p>
                <div class="mt-2 small px-2 py-1 bg-white bg-opacity-10 rounded">
                    <div class="d-flex justify-content-between">
                        <span>All Payments:</span>
                        <span>+<?= number_format($totalPaymentsToday + $standaloneRepayments, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Returns:</span>
                        <span class="text-warning">-<?= number_format($todayReturns, 2) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Realized Net Profit -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 bg-success text-white overflow-hidden" style="background: linear-gradient(135deg, #198754 0%, #157347 100%);">
            <div class="card-body position-relative">
                <div class="position-absolute top-0 end-0 p-3 opacity-10">
                    <span class="material-symbols-outlined" style="font-size: 80px;">trending_up</span>
                </div>
                <h6 class="text-white-50 small text-uppercase fw-bold mb-1">Realized Net Profit</h6>
                <h3 class="fw-bold mb-1">GH₵ <?= number_format($realizedNetProfit, 2) ?></h3>
                <p class="small mb-0 text-white-50">From realized revenue today</p>
                <div class="mt-2 small px-2 py-1 bg-white bg-opacity-10 rounded">
                    <div class="d-flex justify-content-between">
                        <span>Realized Gross:</span>
                        <span><?= number_format($realizedGrossProfit, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between border-top border-white border-opacity-25 pt-1">
                        <span>Expenses:</span>
                        <span class="text-warning">-<?= number_format($dailyExpenditure, 2) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Net Profit (Invoiced) -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 bg-info text-white overflow-hidden" style="background: linear-gradient(135deg, #0dcaf0 0%, #0bacce 100%);">
            <div class="card-body position-relative">
                <div class="position-absolute top-0 end-0 p-3 opacity-10">
                    <span class="material-symbols-outlined" style="font-size: 80px;">receipt_long</span>
                </div>
                <h6 class="text-white-50 small text-uppercase fw-bold mb-1">Daily Net Profit</h6>
                <h3 class="fw-bold mb-1">GH₵ <?= number_format($dailyNetProfit, 2) ?></h3>
                <p class="small mb-0 text-white-50">From invoices created today</p>
                <div class="mt-2 small px-2 py-1 bg-white bg-opacity-10 rounded">
                    <div class="d-flex justify-content-between">
                        <span>Potential Gross:</span>
                        <span><?= number_format($potentialProfit, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between border-top border-white border-opacity-25 pt-1">
                        <span>Expenses:</span>
                        <span class="text-warning">-<?= number_format($dailyExpenditure, 2) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Invoiced Sales -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 bg-white overflow-hidden">
            <div class="card-body position-relative">
                <div class="position-absolute top-0 end-0 p-3 text-muted opacity-10">
                    <span class="material-symbols-outlined" style="font-size: 80px;">shopping_bag</span>
                </div>
                <h6 class="text-muted small text-uppercase fw-bold mb-1">Total Invoiced Sales</h6>
                <h3 class="fw-bold mb-1 text-dark">GH₵ <?= number_format($dailySales, 2) ?></h3>
                <p class="small mb-0 text-muted">Value of all sales recorded</p>
                <div class="mt-2 small p-2 bg-light rounded text-dark border">
                    <div class="d-flex justify-content-between">
                        <span>Returns Val:</span>
                        <span class="text-danger">GH₵ <?= number_format($todayReturns, 2) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Financial Movements -->
    <div class="col-xl-8">
        
        <!-- Sales & Collections Breakdown -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-primary">analytics</span>
                    Sales & Collections Today
                </h5>
                <span class="badge bg-primary rounded-pill">GH₵ <?= number_format($totalPaymentsToday, 2) ?> Total Payments</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="bg-light text-muted small text-uppercase fw-bold">
                            <tr>
                                <th class="ps-4">Invoice #</th>
                                <th>Customer</th>
                                <th>Type</th>
                                <th class="text-end pe-4">Collected</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Today's New Invoices -->
                            <?php if (!empty($todayInvoicesList)): ?>
                                <tr class="bg-light fw-bold small text-muted">
                                    <td colspan="4" class="ps-4 py-2">Today's New Invoices (Created today)</td>
                                </tr>
                                <?php foreach ($todayInvoicesList as $s): ?>
                                <tr>
                                    <td class="ps-4"><a href="<?= BASE_URL ?>/sales/view?id=<?= $s['id'] ?>" class="text-decoration-none fw-bold">#<?= e($s['id']) ?></a></td>
                                    <td><?= e($s['customer_name'] ?: 'General Customer') ?></td>
                                    <td>
                                        <?php if($s['payment_status'] === 'paid'): ?>
                                            <span class="badge bg-success-subtle text-success fw-normal">Paid</span>
                                        <?php elseif($s['payment_status'] === 'partial'): ?>
                                            <span class="badge bg-warning-subtle text-warning fw-normal">Partial</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger fw-normal">Unpaid</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4 fw-bold">GH₵ <?= number_format($s['paid_amount'], 2) ?> <small class="text-muted fw-normal">/ <?= number_format($s['total_amount'], 2) ?></small></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php if (empty($todayInvoicesList) && empty($debtRecoveredList)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No sales or payments recorded for this day</td></tr>
                            <?php endif; ?>
                            
                            <!-- Debt Recovered (Payments for past sales) -->
                            <?php if (!empty($debtRecoveredList)): ?>
                                <tr class="bg-light fw-bold small text-muted">
                                    <td colspan="4" class="ps-4 py-2">Debt Recovery (Payments for old invoices)</td>
                                </tr>
                                <?php foreach ($debtRecoveredList as $p): ?>
                                <tr>
                                    <td class="ps-4"><a href="<?= BASE_URL ?>/sales/view?id=<?= $p['id'] ?>" class="text-decoration-none fw-bold">#<?= e($p['id']) ?></a></td>
                                    <td><?= e($p['customer_name'] ?: 'General Customer') ?></td>
                                    <td><span class="text-success small">Debt Payment</span></td>
                                    <td class="text-end pe-4 fw-bold">GH₵ <?= number_format($p['amount'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Generic Placeholder if we wanted to list ALL payments here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Standalone Debt Activity -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined text-danger">person_remove</span>
                            New Standalone Debts
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="bg-light text-muted small text-uppercase fw-bold">
                                    <tr>
                                        <th class="ps-3">Debtor</th>
                                        <th class="text-end pe-3">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($newDebtorsList)): ?>
                                        <tr><td colspan="2" class="text-center py-3 text-muted">No new debts recorded</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($newDebtorsList as $d): ?>
                                        <tr>
                                            <td class="ps-3"><?= e($d['name']) ?></td>
                                            <td class="text-end pe-3 fw-bold text-danger">GH₵ <?= number_format($d['total_amount'], 2) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined text-success">person_check</span>
                            Standalone Repayments
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="bg-light text-muted small text-uppercase fw-bold">
                                    <tr>
                                        <th class="ps-3">Debtor</th>
                                        <th class="text-end pe-3">Paid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($standaloneRepaymentsList)): ?>
                                        <tr><td colspan="2" class="text-center py-3 text-muted">No repayments recorded</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($standaloneRepaymentsList as $sr): ?>
                                        <tr>
                                            <td class="ps-3"><?= e($sr['debtor_name']) ?></td>
                                            <td class="text-end pe-3 fw-bold text-success">GH₵ <?= number_format($sr['amount'], 2) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Returns Breakdown -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-warning">keyboard_return</span>
                    Returned Items Today
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="bg-light text-muted small text-uppercase fw-bold">
                            <tr>
                                <th class="ps-4">Item Name</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Salesperson</th>
                                <th class="text-end pe-4">Total Deduction</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($returnedItems)): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">No returns recorded for this day</td></tr>
                            <?php else: ?>
                                <?php foreach ($returnedItems as $ri): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark"><?= e($ri['name']) ?></div>
                                        <div class="smaller text-muted">From Invoice <a href="<?= BASE_URL ?>/sales/view?id=<?= $ri['sale_id'] ?>">#<?= e($ri['sale_id']) ?></a></div>
                                    </td>
                                    <td><?= $ri['quantity'] ?></td>
                                    <td>GH₵ <?= number_format($ri['price_at_sale'], 2) ?></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary fw-normal"><?= e($ri['recorder']) ?></span></td>
                                    <td class="text-end pe-4 fw-bold text-danger">GH₵ <?= number_format($ri['total'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Activity Logs -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-info">history</span>
                    System Activity Logs
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="bg-light text-muted small text-uppercase fw-bold sticky-top">
                            <tr>
                                <th class="ps-4">Time</th>
                                <th>Action</th>
                                <th>Operator</th>
                                <th class="pe-4">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($logs)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No activities recorded today</td></tr>
                            <?php else: ?>
                                <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td class="ps-4 text-muted"><?= date('h:i A', strtotime($log['created_at'])) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $log['action'] === 'created' ? 'success' : ($log['action'] === 'stock_adjustment' ? 'info' : 'secondary') ?> bg-opacity-10 text-dark border">
                                            <?= ucfirst(str_replace('_', ' ', $log['action'])) ?>
                                        </span>
                                    </td>
                                    <td><span class="text-secondary small"><?= e($log['operator_name']) ?></span></td>
                                    <td class="pe-4">
                                        <?php if (!empty($log['item_name'])): ?>
                                            <span class="fw-bold text-dark d-block mb-1"><?= e($log['item_name']) ?></span>
                                        <?php endif; ?>
                                        <div class="text-muted smaller" style="max-width: 400px;"><?= e($log['details']) ?></div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Right Column: Expenditures & More -->
    <div class="col-xl-4">
        
        <!-- Expenditure List -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-danger">payments</span>
                    Expenditures
                </h5>
                <span class="fw-bold text-danger">GH₵ <?= number_format($dailyExpenditure, 2) ?></span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush small">
                    <?php if (empty($expendituresList)): ?>
                        <div class="p-4 text-center text-muted">No expenses recorded</div>
                    <?php else: ?>
                        <?php foreach ($expendituresList as $exp): ?>
                        <div class="list-group-item border-0 border-bottom">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="badge bg-danger-subtle text-danger"><?= e($exp['category']) ?></span>
                                <span class="fw-bold">GH₵ <?= number_format($exp['amount'], 2) ?></span>
                            </div>
                            <div class="text-dark fw-bold mb-1"><?= e($exp['description']) ?></div>
                            <div class="d-flex justify-content-between text-muted smaller">
                                <span>Recorded by: <?= e($exp['recorder']) ?></span>
                                <span><?= date('h:i A', strtotime($exp['created_at'])) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 bg-light p-4 text-muted small">
            <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-3">
                <span class="material-symbols-outlined">info</span>
                Formula Key
            </h6>
            <ul class="list-unstyled mb-0">
                <li class="mb-2"><strong>Total Net Collection</strong>: All cash/digital payments received today (New Sales + Debt Recovery + Repayments) minus all Returns handled today.</li>
                <li class="mb-2"><strong>Realized Net Profit</strong>: Gross profit extracted from today's cash collections minus today's expenses.</li>
                <li><strong>Report Integrity</strong>: This report pulls live data from payments, returns, and inventory logs to provide absolute accountability.</li>
            </ul>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
