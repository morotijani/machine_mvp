<?php
$title = "Invoice #" . $sale['id'];
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-8">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom no-print">
        <h1 class="h2">Invoice #<?php echo $sale['id']; ?></h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="<?= BASE_URL ?>/sales" class="btn btn-sm btn-outline-secondary me-2">Back to History</a>
            <button onclick="window.print()" class="btn btn-sm btn-primary d-flex align-items-center gap-2">
                <span class="fs-5">🖨</span> Print Invoice
            </button>
        </div>
    </div>

    <div class="card shadow-sm" id="invoice-card">
        <div class="card-body p-5">
            <!-- Header -->
            <div class="row mb-5">
                <div class="col-6">
                    <h2 class="text-primary fw-bold">Machine Shop</h2>
                    <p class="text-muted">
                        123 Industrial Road<br>
                        City, State, Zip
                    </p>
                </div>
                <div class="col-6 text-end">
                    <h4 class="text-muted">INVOICE</h4>
                    <p>
                        <strong>Invoice No:</strong> #<?php echo $sale['id']; ?><br>
                        <strong>Date:</strong> <?php echo date('M j, Y h:i A', strtotime($sale['created_at'])); ?><br>
                        <strong>Salesperson:</strong> <?php echo htmlspecialchars($sale['seller_name']); ?>
                    </p>
                </div>
            </div>

            <!-- Bill To -->
            <div class="row mb-5">
                <div class="col-6">
                    <p class="mb-1 text-uppercase text-muted small fw-bold">Bill To</p>
                    <?php if ($sale['customer_name']): ?>
                        <h5 class="fw-bold"><?php echo htmlspecialchars($sale['customer_name']); ?></h5>
                        <p>
                            <?php echo htmlspecialchars($sale['customer_address'] ?? ''); ?><br>
                            <?php echo htmlspecialchars($sale['customer_phone'] ?? ''); ?>
                        </p>
                    <?php else: ?>
                        <h5 class="fw-bold text-muted">Walk-in Customer</h5>
                    <?php endif; ?>
                </div>
                <div class="col-6 text-end">
                    <p class="mb-1 text-uppercase text-muted small fw-bold">Payment Status</p>
                    <?php if ($sale['payment_status'] === 'paid'): ?>
                        <span class="badge bg-success fs-5">PAID</span>
                    <?php elseif ($sale['payment_status'] === 'partial'): ?>
                        <span class="badge bg-warning text-dark fs-5">PARTIAL CREDIT</span>
                    <?php else: ?>
                        <span class="badge bg-danger fs-5">UNPAID</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Items -->
            <div class="table-responsive mb-4">
                <table class="table table-striped">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="py-3">Item</th>
                            <th class="py-3 text-center">SKU</th>
                            <th class="py-3 text-center">Qty</th>
                            <th class="py-3 text-end">Unit Price</th>
                            <th class="py-3 text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sale['items'] as $item): ?>
                        <tr>
                            <td class="fw-bold"><?php echo htmlspecialchars($item['item_name']); ?></td>
                            <td class="text-center small text-muted"><?php echo htmlspecialchars($item['sku']); ?></td>
                            <td class="text-center"><?php echo $item['quantity']; ?></td>
                            <td class="text-end">₵<?php echo number_format($item['price_at_sale'], 2); ?></td>
                            <td class="text-end">₵<?php echo number_format($item['subtotal'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Total Amount</td>
                            <td class="text-end fw-bold fs-5">₵<?php echo number_format($sale['total_amount'], 2); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end">Amount Paid</td>
                            <td class="text-end text-success fw-bold">-₵<?php echo number_format($sale['paid_amount'], 2); ?></td>
                        </tr>
                        <?php if ($sale['total_amount'] - $sale['paid_amount'] > 0): ?>
                        <tr>
                            <td colspan="4" class="text-end fw-bold text-danger">Balance Due</td>
                            <td class="text-end fw-bold text-danger fs-5">₵<?php echo number_format($sale['total_amount'] - $sale['paid_amount'], 2); ?></td>
                        </tr>
                        <?php endif; ?>
                    </tfoot>
                </table>
            </div>

            <?php if (!empty($payments)): ?>
            <div class="mb-4">
                <h6 class="text-muted text-uppercase small fw-bold">Payment History</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Received By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?php echo date('M j, Y H:i', strtotime($payment['payment_date'])); ?></td>
                                <td class="text-success fw-bold">₵<?php echo number_format($payment['amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($payment['username']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Footer -->
            <div class="text-center mt-5 text-muted no-print">
                <?php if ($sale['total_amount'] - $sale['paid_amount'] > 0): ?>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#payModal">₵₵ Record Payment</button>
                <?php else: ?>
                    <p class="text-success fw-bold">Fully Paid</p>
                <?php endif; ?>
            </div>
            
            <div class="text-center mt-3 text-muted print-only">
                <p>Thank you for your business!</p>
            </div>
        </div>
    </div>

    <!-- Pay Modal -->
    <div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Record Payment</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="<?= BASE_URL ?>/sales/pay" method="POST">
            <input type="hidden" name="sale_id" value="<?php echo $sale['id']; ?>">
            <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Amount Received</label>
                        <div class="input-group">
                            <span class="input-group-text">₵</span>
                            <input type="number" name="amount" step="0.01" class="form-control" max="<?php echo round($sale['total_amount'] - $sale['paid_amount'], 2); ?>" required>
                        </div>
                        <div class="form-text">Max due: ₵<?php echo number_format($sale['total_amount'] - $sale['paid_amount'], 2); ?></div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Save Payment</button>
            </div>
        </form>
        </div>
    </div>
    </div>

<?php
    $content = ob_get_clean();
    require __DIR__ . '/../layouts/main.php';
?>

