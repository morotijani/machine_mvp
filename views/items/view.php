<?php
$title = "Item Detail: " . e($item['name']);
ob_start();
?>

<style>
    .google-table-card {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 1px 2px 0 rgba(60, 64, 67, 0.3), 0 1px 3px 1px rgba(60, 64, 67, 0.15);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .google-btn-secondary {
        background: transparent;
        border: 1px solid #dadce0;
        color: #1f1f1f;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 14px;
        text-decoration: none;
        transition: background 0.2s;
    }
    .google-btn-secondary:hover {
        background: #f8f9fa;
        color: #1f1f1f;
    }
    .google-btn-primary {
        background: #0b57d0;
        border: none;
        color: #fff;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 14px;
        text-decoration: none;
        transition: background 0.2s;
    }
    .google-btn-primary:hover {
        background: #0842a0;
        color: #fff;
    }
    .google-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        letter-spacing: 0.5px;
        font-weight: 600;
        display: inline-block;
    }
    .badge-subtle-success {
        background-color: #e6f4ea;
        color: #137333;
        border: 1px solid #ceead6;
    }
    .badge-subtle-danger {
        background-color: #fce8e6;
        color: #c5221f;
        border: 1px solid #fad2cf;
    }
    .badge-subtle-primary {
        background-color: #e8f0fe;
        color: #1967d2;
        border: 1px solid #d2e3fc;
    }
    .badge-subtle-warning {
        background-color: #fef7e0;
        color: #b06000;
        border: 1px solid #fce8b2;
    }
    .table-custom tbody td {
        padding: 16px 24px;
        border-bottom: 1px solid #e3e3e3;
        color: #1f1f1f;
        vertical-align: middle;
    }
    .table-custom tbody tr:last-child td {
        border-bottom: none;
    }
    .table-custom th {
        padding: 12px 24px;
        font-size: 12px;
        text-transform: uppercase;
        color: #5f6368;
        font-weight: 600;
        border-bottom: 1px solid #e3e3e3;
    }
    .table-custom tbody tr:hover {
        background-color: #f8f9fa;
    }
    .section-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e3e3e3;
        font-weight: 500;
        color: #1f1f1f;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .detail-label {
        font-size: 12px;
        text-transform: uppercase;
        color: #5f6368;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .detail-value {
        font-size: 16px;
        color: #1f1f1f;
        font-weight: 500;
    }
    @media print {
        .navbar, .sidebar, .no-print, .btn, .google-btn-secondary, .google-btn-primary {
            display: none !important;
        }
        .google-table-card {
            border: 1px solid #dee2e6 !important;
            box-shadow: none !important;
        }
        body {
            background-color: white !important;
        }
    }
</style>

<div class="row justify-content-center">
    <div class="col-12 col-xxl-11">
        <!-- Back and Title -->
        <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-4 gap-2 no-print">
            <div class="d-flex align-items-center gap-3">
                <a href="<?= $_SESSION['last_items_url'] ?? (BASE_URL . '/items') ?>" class="google-btn-secondary d-flex align-items-center gap-1 border-0" style="padding: 8px;">
                    <span class="material-symbols-outlined align-middle" style="font-size: 20px;">arrow_back</span>
                </a>
                <h1 class="h3 mb-0 fw-normal" style="color: #1f1f1f;">Item Detail</h1>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button onclick="window.print()" class="google-btn-secondary d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size: 18px;">print</span> Print Report
                </button>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="<?= BASE_URL ?>/items/edit?id=<?= $item['id'] ?>" class="google-btn-primary d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size: 18px;">edit</span> Edit Item
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Header Card -->
        <div class="google-table-card">
            <div class="row g-0">
                <div class="<?= $item['type'] === 'bundle' ? 'col-md-8' : 'col-12' ?> p-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0" style="width: 64px; height: 64px; background: #f8f9fa; border-radius: 16px; border: 1px solid #e3e3e3;">
                            <?php if (!empty($item['image_path'])): ?>
                                <img src="<?= BASE_URL ?>/<?= $item['image_path'] ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 15px;">
                            <?php else: ?>
                                <span class="material-symbols-outlined text-muted fs-1"><?= $item['type'] === 'bundle' ? 'inventory_2' : 'package_2' ?></span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h3 class="fw-normal mb-2" style="color: #1f1f1f; font-size: 24px;"><?= e($item['name']) ?></h3>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="google-badge" style="background: #f1f3f4; color: #444746;">SKU: <?= e($item['sku'] ?: 'N/A') ?></span>
                                <span class="google-badge badge-subtle-primary"><?= e($item['category']) ?></span>
                                <?php if ($item['type'] === 'bundle'): ?>
                                    <span class="google-badge badge-subtle-warning">Bundle</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4 pt-4 g-4" style="border-top: 1px solid #e3e3e3;">
                        <div class="col-12 col-sm-4">
                            <div class="detail-label">Current Stock</div>
                            <div class="detail-value">
                                <span class="google-badge <?= $item['quantity'] <= 5 ? 'badge-subtle-danger' : 'badge-subtle-success' ?>">
                                    <?= $item['quantity'] ?> <?= e($item['unit']) ?>s
                                </span>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="detail-label">Selling Price</div>
                            <div class="detail-value text-primary">₵<?= number_format($item['price'], 2) ?></div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="detail-label">Location</div>
                            <div class="detail-value"><?= e($item['location'] ?: 'N/A') ?></div>
                        </div>
                    </div>
                </div>
                
                <?php if ($item['type'] === 'bundle'): ?>
                <div class="col-md-4 p-4" style="background: #f8f9fa; border-left: 1px solid #e3e3e3;">
                    <div class="detail-label mb-3 d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined text-info" style="font-size: 18px;">hub</span>
                        Bundle Composition
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($components as $comp): ?>
                        <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background: #fff; border: 1px solid #e3e3e3;">
                            <div>
                                <div class="fw-medium text-dark" style="font-size: 14px;"><?= e($comp['name']) ?></div>
                                <div class="text-muted" style="font-size: 12px;">SKU: <?= e($comp['child_sku']) ?></div>
                            </div>
                            <span class="google-badge" style="background: #f1f3f4; color: #1f1f1f;">x<?= $comp['quantity'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sales History Timeline -->
        <div class="google-table-card">
            <div class="section-header">
                <span class="material-symbols-outlined text-primary">analytics</span>
                Sales History
            </div>
            
            <?php if (empty($salesHistory)): ?>
                <div class="text-center py-5">
                    <span class="material-symbols-outlined text-muted mb-3" style="font-size: 48px;">shopping_cart_off</span>
                    <p class="text-muted" style="font-size: 15px;">This product hasn't been sold yet.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-borderless table-custom align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th class="text-center">Qty Sold</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($salesHistory as $sale): ?>
                            <tr>
                                <td>
                                    <div class="fw-medium"><?= date('M j, Y', strtotime($sale['created_at'])) ?></div>
                                    <div class="text-muted" style="font-size: 12px;"><?= date('h:i A', strtotime($sale['created_at'])) ?></div>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/sales/view?id=<?= $sale['sale_id'] ?>" style="color: #0b57d0; text-decoration: none; font-weight: 500;">
                                        #<?= $sale['sale_id'] ?>
                                    </a>
                                    <?php if (!empty($sale['bundle_name'])): ?>
                                        <div class="mt-1">
                                            <span class="google-badge badge-subtle-warning" style="font-size: 10px;" title="Sold as part of bundle">via <?= e($sale['bundle_name']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($sale['customer_id']): ?>
                                        <a href="<?= BASE_URL ?>/customers/view?id=<?= $sale['customer_id'] ?>" style="color: #1f1f1f; text-decoration: none;">
                                            <?= e($sale['customer_name']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic">Walk-in</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="google-badge" style="background: #f1f3f4; color: #1f1f1f;"><?= $sale['quantity'] ?></span>
                                </td>
                                <td class="text-end">
                                    <?php if (!empty($sale['bundle_name'])): ?>
                                        <span class="text-muted" style="font-size: 13px;">Included</span>
                                    <?php else: ?>
                                        ₵<?= number_format($sale['price_at_sale'], 2) ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end fw-bold text-primary">
                                    <?php if (!empty($sale['bundle_name'])): ?>
                                        -
                                    <?php else: ?>
                                        ₵<?= number_format($sale['subtotal'], 2) ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($totalSalesPages > 1): ?>
                <div class="p-3" style="border-top: 1px solid #e3e3e3;">
                    <nav aria-label="Sales History Pagination">
                        <ul class="pagination justify-content-center mb-0 gap-2">
                            <?php if ($salesPage > 1): ?>
                                <li class="page-item">
                                    <a class="google-btn-secondary" href="?id=<?= $item['id'] ?>&sales_page=<?= $salesPage - 1 ?>&logs_page=<?= $logsPage ?>">Previous</a>
                                </li>
                            <?php endif; ?>
                            <li class="page-item disabled d-flex align-items-center">
                                <span class="text-muted px-3" style="font-size: 14px;">Page <?= $salesPage ?> of <?= $totalSalesPages ?></span>
                            </li>
                            <?php if ($salesPage < $totalSalesPages): ?>
                                <li class="page-item">
                                    <a class="google-btn-secondary" href="?id=<?= $item['id'] ?>&sales_page=<?= $salesPage + 1 ?>&logs_page=<?= $logsPage ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Parent Bundles -->
        <?php if (!empty($parentBundles)): ?>
        <div class="google-table-card">
            <div class="section-header">
                <span class="material-symbols-outlined text-primary">inventory_2</span>
                Included in Bundles
            </div>
            
            <div class="table-responsive">
                <table class="table table-borderless table-custom align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Bundle Name</th>
                            <th>Category</th>
                            <th class="text-center">Qty per Bundle</th>
                            <th class="text-center">Total Bundle Stock</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($parentBundles as $bundle): ?>
                        <tr>
                            <td>
                                <div class="fw-medium text-dark"><?= e($bundle['name']) ?></div>
                                <div class="text-muted" style="font-size: 12px;"><?= e($bundle['sku']) ?></div>
                            </td>
                            <td><span class="google-badge badge-subtle-primary"><?= e($bundle['category']) ?></span></td>
                            <td class="text-center fw-bold" style="color: #0b57d0;"><?= $bundle['qty_required'] ?></td>
                            <td class="text-center">
                                <span class="google-badge <?= $bundle['quantity'] > 0 ? 'badge-subtle-success' : 'badge-subtle-danger' ?>">
                                    <?= $bundle['quantity'] ?> available
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= BASE_URL ?>/items/view?id=<?= $bundle['id'] ?>" class="google-btn-secondary" style="font-size: 12px;">View Bundle</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Activity & Stock Logs -->
        <div class="google-table-card">
            <div class="section-header">
                <span class="material-symbols-outlined text-info">history</span>
                Activity & Stock Logs
            </div>

            <?php if (empty($activityLogs)): ?>
                <div class="text-center py-5">
                    <span class="material-symbols-outlined text-muted mb-3" style="font-size: 48px;">history_toggle_off</span>
                    <p class="text-muted" style="font-size: 15px;">No activity logs recorded for this item.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-borderless table-custom align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Action</th>
                                <th>Details</th>
                                <th class="text-center">Old Qty</th>
                                <th class="text-center">New Qty</th>
                                <th>Operator</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activityLogs as $log): ?>
                            <tr>
                                <td class="text-nowrap">
                                    <div class="fw-medium"><?= date('M j, Y', strtotime($log['created_at'])) ?></div>
                                    <div class="text-muted" style="font-size: 12px;"><?= date('h:i A', strtotime($log['created_at'])) ?></div>
                                </td>
                                <td>
                                    <span class="google-badge <?= $log['action'] === 'created' ? 'badge-subtle-success' : ($log['action'] === 'stock_adjustment' ? 'badge-subtle-primary' : 'badge-subtle-warning') ?>">
                                        <?= ucfirst(str_replace('_', ' ', $log['action'])) ?>
                                    </span>
                                </td>
                                <td><div style="max-width: 300px; font-size: 13px; color: #5f6368;"><?= e($log['details']) ?></div></td>
                                <td class="text-center text-muted"><?= is_null($log['old_quantity']) ? '-' : $log['old_quantity'] ?></td>
                                <td class="text-center fw-medium"><?= is_null($log['new_quantity']) ? '-' : $log['new_quantity'] ?></td>
                                <td class="text-nowrap">
                                    <span class="google-badge" style="background: #f1f3f4; color: #444746;"><?= e($log['operator_name']) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($totalLogsPages > 1): ?>
                <div class="p-3" style="border-top: 1px solid #e3e3e3;">
                    <nav aria-label="Activity Logs Pagination">
                        <ul class="pagination justify-content-center mb-0 gap-2">
                            <?php if ($logsPage > 1): ?>
                                <li class="page-item">
                                    <a class="google-btn-secondary" href="?id=<?= $item['id'] ?>&sales_page=<?= $salesPage ?>&logs_page=<?= $logsPage - 1 ?>">Previous</a>
                                </li>
                            <?php endif; ?>
                            <li class="page-item disabled d-flex align-items-center">
                                <span class="text-muted px-3" style="font-size: 14px;">Page <?= $logsPage ?> of <?= $totalLogsPages ?></span>
                            </li>
                            <?php if ($logsPage < $totalLogsPages): ?>
                                <li class="page-item">
                                    <a class="google-btn-secondary" href="?id=<?= $item['id'] ?>&sales_page=<?= $salesPage ?>&logs_page=<?= $logsPage + 1 ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
