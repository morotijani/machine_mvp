<?php
$title = "Item Detail: " . e($item['name']);
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-11">
        <!-- Back and Title -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 no-print">
            <div class="d-flex align-items-center gap-2">
                <a href="<?= $_SESSION['last_items_url'] ?? (BASE_URL . '/items') ?>" class="btn btn-outline-secondary btn-sm">
                    <span class="material-symbols-outlined align-middle" style="font-size: 18px;">arrow_back</span>
                </a>
                <h1 class="h2 mb-0">Item Detail</h1>
            </div>
            <div class="btn-toolbar mb-2 mb-md-0 gap-2">
                <button onclick="window.print()" class="btn btn-sm btn-outline-dark d-flex align-items-center gap-1">
                    <span class="material-symbols-outlined" style="font-size: 16px;">print</span> Print Report
                </button>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="<?= BASE_URL ?>/items/edit?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                    <span class="material-symbols-outlined" style="font-size: 16px;">edit</span> Edit Item
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Header Card -->
        <div class="card shadow-sm border-0 mb-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-md-8 p-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-4 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                <span class="material-symbols-outlined fs-1"><?= $item['type'] === 'bundle' ? 'inventory_2' : 'package_2' ?></span>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-1"><?= e($item['name']) ?></h3>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge bg-light text-dark border shadow-sm">SKU: <?= e($item['sku']) ?></span>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border-primary border-opacity-25"><?= e($item['category']) ?></span>
                                    <?php if ($item['type'] === 'bundle'): ?>
                                        <span class="badge bg-info bg-opacity-10 text-info border-info border-opacity-25">Bundle</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4 pt-3 border-top">
                            <div class="col-4">
                                <label class="text-muted small d-block text-uppercase fw-bold mb-1">Current Stock</label>
                                <div class="h5 mb-0 d-flex align-items-center gap-2">
                                    <span class="badge <?= $item['quantity'] <= 5 ? 'bg-danger' : 'bg-success' ?> rounded-pill px-3">
                                        <?= $item['quantity'] ?> <?= e($item['unit']) ?>s
                                    </span>
                                </div>
                            </div>
                            <div class="col-4 border-start border-end">
                                <label class="text-muted small d-block text-uppercase fw-bold mb-1">Selling Price</label>
                                <div class="h4 mb-0 fw-bold text-primary">₵<?= number_format($item['price'], 2) ?></div>
                            </div>
                            <div class="col-4">
                                <label class="text-muted small d-block text-uppercase fw-bold mb-1">Location</label>
                                <div class="h6 mb-0 text-dark"><?= e($item['location'] ?: 'N/A') ?></div>
                            </div>
                        </div>
                    </div>
                    <?php if ($item['type'] === 'bundle'): ?>
                    <div class="col-md-4 bg-light p-4 border-start">
                        <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined text-info" style="font-size: 18px;">hub</span>
                            Bundle Composition
                        </h6>
                        <div class="list-group list-group-flush small bg-transparent">
                            <?php foreach ($components as $comp): ?>
                            <div class="list-group-item bg-transparent px-0 py-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-dark"><?= e($comp['name']) ?></div>
                                    <div class="text-muted smaller">SKU: <?= e($comp['child_sku']) ?></div>
                                </div>
                                <span class="badge bg-white text-dark border">x<?= $comp['quantity'] ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sales History Timeline -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-primary">analytics</span>
                    Sales History & Distribution
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($salesHistory)): ?>
                    <div class="text-center py-5">
                        <span class="material-symbols-outlined fs-1 text-muted mb-3">shopping_cart_off</span>
                        <p class="text-muted">This product hasn't been sold yet.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase fw-bold">
                                <tr>
                                    <th class="ps-4">Date & Time</th>
                                    <th>Invoice #</th>
                                    <th>Customer</th>
                                    <th class="text-center">Qty Sold</th>
                                    <th class="text-end">Price at Sale</th>
                                    <th class="text-end pe-4">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($salesHistory as $sale): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold"><?= date('M j, Y', strtotime($sale['created_at'])) ?></div>
                                        <div class="text-muted smaller"><?= date('h:i A', strtotime($sale['created_at'])) ?></div>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>/sales/view?id=<?= $sale['sale_id'] ?>" class="text-decoration-none fw-bold">
                                            #<?= $sale['sale_id'] ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if ($sale['customer_id']): ?>
                                            <a href="<?= BASE_URL ?>/customers/view?id=<?= $sale['customer_id'] ?>" class="text-decoration-none text-dark">
                                                <?= e($sale['customer_name']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted italic">Walk-in Customer</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border"><?= $sale['quantity'] ?></span>
                                    </td>
                                    <td class="text-end">₵<?= number_format($sale['price_at_sale'], 2) ?></td>
                                    <td class="text-end pe-4 fw-bold text-primary">₵<?= number_format($sale['subtotal'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- Parent Bundles (If part of any bundle) -->
        <?php if (!empty($parentBundles)): ?>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-primary">inventory_2</span>
                    Included in Bundles
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="bg-light text-muted small text-uppercase fw-bold">
                            <tr>
                                <th class="ps-4">Bundle Name</th>
                                <th>Category</th>
                                <th class="text-center">Qty per Bundle</th>
                                <th class="text-center">Total Bundle Stock</th>
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($parentBundles as $bundle): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark"><?= e($bundle['name']) ?></div>
                                    <div class="text-muted smaller"><?= e($bundle['sku']) ?></div>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= e($bundle['category']) ?></span></td>
                                <td class="text-center fw-bold text-primary"><?= $bundle['qty_required'] ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $bundle['quantity'] > 0 ? 'success' : 'danger' ?> bg-opacity-10 text-dark border">
                                        <?= $bundle['quantity'] ?> available
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="<?= BASE_URL ?>/items/view?id=<?= $bundle['id'] ?>" class="btn btn-sm btn-outline-primary">View Bundle</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Activity & Stock Logs -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-info">history</span>
                    Activity & Stock Logs
                </h5>
            </div>
            <div class="card-body p-0">

                <?php if (empty($activityLogs)): ?>
                    <div class="text-center py-5">
                        <span class="material-symbols-outlined fs-1 text-muted mb-3">history_toggle_off</span>
                        <p class="text-muted">No activity logs recorded for this item.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="bg-light text-muted small text-uppercase fw-bold">
                                <tr>
                                    <th class="ps-4">Date & Time</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th class="text-center">Old Qty</th>
                                    <th class="text-center">New Qty</th>
                                    <th class="pe-4">Operator</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activityLogs as $log): ?>
                                <tr>
                                    <td class="ps-4 text-nowrap">
                                        <div class="fw-bold"><?= date('M j, Y', strtotime($log['created_at'])) ?></div>
                                        <div class="text-muted smaller"><?= date('h:i A', strtotime($log['created_at'])) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge <?= $log['action'] === 'created' ? 'bg-success' : ($log['action'] === 'stock_adjustment' ? 'bg-info' : 'bg-secondary') ?> bg-opacity-10 text-dark border">
                                            <?= ucfirst(str_replace('_', ' ', $log['action'])) ?>
                                        </span>
                                    </td>
                                    <td><div style="max-width: 300px;"><?= e($log['details']) ?></div></td>
                                    <td class="text-center text-muted"><?= is_null($log['old_quantity']) ? '-' : $log['old_quantity'] ?></td>
                                    <td class="text-center fw-bold"><?= is_null($log['new_quantity']) ? '-' : $log['new_quantity'] ?></td>
                                    <td class="pe-4 text-nowrap">
                                        <span class="badge bg-secondary-subtle text-secondary"><?= e($log['operator_name']) ?></span>
                                    </td>
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

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
    }
    .smaller {
        font-size: 0.75rem;
    }
    @media print {
        .navbar, .sidebar, .no-print, .btn {
            display: none !important;
        }
        .card {
            border: 1px solid #dee2e6 !important;
            box-shadow: none !important;
        }
        body {
            background-color: white !important;
        }
    }
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
