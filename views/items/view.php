<?php
$title = "Item Detail: " . e($item['name']);
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-11">
        <!-- Back and Title -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 no-print">
            <div class="d-flex align-items-center gap-2">
                <a href="<?= BASE_URL ?>/items" class="btn btn-outline-secondary btn-sm">
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
