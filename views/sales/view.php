<?php
$title = "Invoice #" . str_pad($sale['id'], 6, '0', STR_PAD_LEFT);
ob_start();
?>

<style>
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        body { background-color: #fff !important; margin: 0; padding: 0; }
        .invoice-card { box-shadow: none !important; border: none !important; margin: 0 !important; padding: 0 !important; max-width: 100% !important; }
        .modal-backdrop, .modal, .navbar, .sidebar { display: none !important; }
        .content-wrapper { margin: 0 !important; padding: 0 !important; }
    }
    .print-only { display: none; }
    .invoice-card { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto; }
    .header-title { font-size: 2rem; font-weight: bold; color: #333; }
    .cancel-watermark {
        position: absolute; top: 40%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg);
        font-size: 5rem; font-weight: bold; border: 8px solid;
        padding: 5px 30px; border-radius: 10px; pointer-events: none;
        opacity: 0.25; z-index: 0; font-family: 'Courier New', Courier, monospace;
        text-transform: uppercase;
        letter-spacing: 5px;
    }
    .watermark-red { color: #dc3545; border-color: #dc3545; }
    .watermark-green { color: #198754; border-color: #198754; } /* Success Green */
    .watermark-yellow { color: #ffca2c; border-color: #ffca2c; } /* Warning Yellow */
</style>

<div class="row justify-content-center">
    <div class="col-md-10">
        
        <!-- Toolbar -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 no-print">
            <h1 class="h2">Invoice #<?= $sale['id'] ?></h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="<?= BASE_URL ?>/sales" class="btn btn-sm btn-outline-secondary me-2">
                    <span class="material-symbols-outlined align-text-bottom" style="font-size: 18px;">arrow_back</span> Back to History
                </a>
                <button onclick="window.print()" class="btn btn-sm btn-primary d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size: 18px;">print</span> Print Invoice
                </button>
            </div>
        </div>

        <div class="invoice-card position-relative">
            <?php if (!empty($sale['voided'])): ?>
                <div class="cancel-watermark watermark-red">VOIDED</div>
            <?php elseif ($sale['payment_status'] === 'paid'): ?>
                <div class="cancel-watermark watermark-green">PAID</div>
            <?php elseif ($sale['payment_status'] === 'partial'): ?>
                <div class="cancel-watermark watermark-yellow">PARTIAL</div>
            <?php else: ?>
                <div class="cancel-watermark watermark-red">UNPAID</div>
            <?php endif; ?>

            <!-- Header -->
            <div class="row mb-4 border-bottom pb-4 align-items-center">
                <div class="col-8">
                     <div class="d-flex align-items-center mb-2">
                        <?php if (!empty($settings['company_logo'])): ?>
                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($settings['company_logo']) ?>" alt="Logo" style="height: 50px; margin-right: 15px;">
                        <?php endif; ?>
                        <h1 class="header-title m-0"><?= htmlspecialchars($settings['company_name']) ?></h1>
                     </div>
                     <div class="text-muted">
                        <?php if(!empty($settings['company_address'])): ?>
                            <?= nl2br(htmlspecialchars($settings['company_address'])) ?><br>
                        <?php endif; ?>
                        <?php if(!empty($settings['company_phone'])): ?>
                            PH: <?= htmlspecialchars($settings['company_phone']) ?><br>
                        <?php endif; ?>
                        <?php if(!empty($settings['company_email'])): ?>
                            Email: <?= htmlspecialchars($settings['company_email']) ?>
                        <?php endif; ?>
                     </div>
                </div>
                <div class="col-4 text-end">
                    <h4 class="fw-bold text-primary">INVOICE</h4>
                    <div class="fs-5">#<?= str_pad($sale['id'], 6, '0', STR_PAD_LEFT) ?></div>
                    <div class="text-muted small">Date: <?= date('M j, Y', strtotime($sale['created_at'])) ?></div>
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
                            <td class="fw-bold">
                                <?php echo htmlspecialchars($item['item_name']); ?>
                                <?php 
                                    // Hacky check: In a real app we'd join 'type' from items table. 
                                    // For now, let's assume if it has sub-components we fetch them. 
                                    // But wait, the sale_items table stores the snapshot. 
                                    // To satisfy the requirement "list full items under them", we should ideally store this snapshots 
                                    // OR look up the bundle definition. Looking up definition is easier but historical accuracy depends on bundle not changing.
                                    // Let's do a live lookup for now as an MVP.
                                    $pdo = \App\Config\Database::getInstance();
                                    $itemModel = new \App\Models\Item($pdo);
                                    // We need to know if it is a bundle. 
                                    // Optimization: Sale::getById already defines 'items'. 
                                    // I'll add a quick lookup here or update Sale Model. 
                                    // Updating Sale Model is cleaner.
                                    // ...
                                    // Actually, let's just do a direct query for simplicity in the view for this specific MVP requirement 
                                    // since I cannot change the Sale Model return structure easily without potentially breaking other things.
                                    $stmtBundle = $pdo->prepare("SELECT i.name, ib.quantity FROM item_bundles ib JOIN items i ON ib.child_item_id = i.id WHERE ib.parent_item_id = :id");
                                    $stmtBundle->execute(['id' => $item['item_id']]);
                                    $components = $stmtBundle->fetchAll();
                                ?>
                                <?php if (!empty($components)): ?>
                                    <div class="small text-muted fw-normal mt-1 ps-3 border-start border-3">
                                        <?php foreach ($components as $comp): ?>
                                            <div><?= $comp['quantity'] ?>x <?= htmlspecialchars($comp['name']) ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
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
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#payModal">₵ Record Payment</button>
                <?php else: ?>
                    <p class="text-success fw-bold">Fully Paid</p>
                <?php endif; ?>
            </div>
        
            <div class="text-center mt-3 text-muted print-only">
                <p>Thank you for your business!</p>
                <span style="font-size: 12px;">Printed on: <?php echo date('M j, Y H:i'); ?></span>
                <br>
                <span style="font-size: 10px;">Mijma Inc. | POS System</span>
            </div>
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
                            <input type="number" name="amount" id="payment-amount" step="0.01" class="form-control" max="<?php echo round($sale['total_amount'] - $sale['paid_amount'], 2); ?>" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('payment-amount').value = '<?php echo round($sale['total_amount'] - $sale['paid_amount'], 2); ?>'">Pay All</button>
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

