<?php
$title = "Items & Machines";
ob_start();

// If this is a print view, cleaner layout is handled by CSS, but we can also auto-trigger print
if (isset($isPrint) && $isPrint) {
    echo '<script>window.onload = function() { window.print(); }</script>';
}
?>
<style>
    @media print {
        .no-print, .btn, .navbar, .sidebar, form.d-flex, .pagination, .page-header-actions { display: none !important; }
        .card { border: none !important; shadow: none !important; }
        .table-responsive { overflow: visible !important; }
        body { background-color: #fff !important; }
        main { margin: 0 !important; padding: 0 !important; }
        /* Hide actions column in print */
        th:last-child, td:last-child { display: none !important; }
        .badge { border: 1px solid #000; color: #000 !important; background: #fff !important; }
        /* Ensure all rows show */
        tr { page-break-inside: avoid; }
    }
</style>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                <h1 class="h2">Items & Machines</h1>
                <div class="d-flex gap-3 align-items-center">
                    <form action="" method="GET" class="d-flex gap-2">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <span class="material-symbols-outlined text-muted" style="font-size: 20px;">search</span>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search items..." value="<?= e($search ?? '') ?>">
</div>
                        
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <span class="material-symbols-outlined text-muted" style="font-size: 20px;">sort</span>
                            </span>
                            <select name="sort" class="form-select border-start-0 ps-0" onchange="this.form.submit()">
                                <option value="name" <?= ($sort == 'name') ? 'selected' : '' ?>>Name</option>
                                <option value="price" <?= ($sort == 'price') ? 'selected' : '' ?>>Price</option>
                                <option value="quantity" <?= ($sort == 'quantity') ? 'selected' : '' ?>>Stock Level</option>
                                <option value="created_at" <?= ($sort == 'created_at') ? 'selected' : '' ?>>Date Added</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <select name="order" class="form-select" onchange="this.form.submit()">
                                <option value="ASC" <?= ($order == 'ASC') ? 'selected' : '' ?>>Ascending</option>
                                <option value="DESC" <?= ($order == 'DESC') ? 'selected' : '' ?>>Descending</option>
                            </select>
                        </div>
                        
                        <div class="btn-group">
                            <input type="checkbox" name="low_stock" value="1" class="btn-check" id="lowStockCheck" <?= ($lowStock ?? false) ? 'checked' : '' ?> onchange="this.form.submit()">
                            <label class="btn btn-outline-danger d-flex align-items-center gap-2" for="lowStockCheck">
                                <span class="material-symbols-outlined" style="font-size: 18px;">warning</span> Low Stock
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary d-none">Filter</button>
                        <?php if (!empty($search) || ($lowStock ?? false) || $sort !== 'created_at' || $order !== 'DESC'): ?>
                            <a href="<?= BASE_URL ?>/items" class="btn btn-outline-secondary">Clear</a>
                        <?php endif; ?>
                    </form>

                    <div class="btn-toolbar mb-2 mb-md-0 gap-2 page-header-actions">
                        <?php 
                            // Preserve current search/sort params for print link
                            $printParams = $_GET;
                            $printParams['print'] = 1;
                            // Reset page to 1 to get all from start
                            $printParams['page'] = 1;
                        ?>
                        <div class="d-flex gap-2">
                             <?php if (isset($isPrint) && $isPrint): ?>
                                <a href="<?= BASE_URL ?>/items" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2">
                                    <span class="material-symbols-outlined" style="font-size: 18px;">arrow_back</span> Back
                                </a>
                            <?php else: ?>
                                <a href="?<?= http_build_query($printParams) ?>" class="btn btn-sm btn-outline-dark d-flex align-items-center gap-2" target="_blank">
                                    <span class="material-symbols-outlined" style="font-size: 18px;">print</span> Print List
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($_SESSION['role'] === 'admin' && (!isset($isPrint) || !$isPrint)): ?>
                                <a href="<?= BASE_URL ?>/items/create-bundle" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-2">
                                    <span class="material-symbols-outlined" style="font-size: 18px;">inventory_2</span> New Bundle
                                </a>
                                <a href="<?= BASE_URL ?>/items/create" class="btn btn-sm btn-primary d-flex align-items-center gap-2">
                                    <span class="fs-5">+</span> New Item
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <span class="material-symbols-outlined align-middle me-2">check_circle</span>
                    <?= htmlspecialchars($_GET['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <span class="material-symbols-outlined align-middle me-2">error</span>
                    <?= htmlspecialchars($_GET['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                <th style="width: 50px;">#</th>
                                <th style="width: 60px;">Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>SKU / Barcode</th>
                                <th>Location</th>
                                <th class="text-end">Price</th>
                                <th class="text-center">Stock</th>
                                <?php if ($_SESSION['role'] === 'admin'): ?>
                                <th class="text-end">Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $startNum = ($page - 1) * 10 + 1;
                                foreach ($items as $index => $item): 
                            ?>
                            <tr>
                                <td class="text-muted small"><?= $startNum + $index ?></td>
                                <td>
                                    <?php if (!empty($item['image_path'])): ?>
                                        <img src="<?= BASE_URL ?>/<?php echo $item['image_path']; ?>" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted small" style="width: 40px; height: 40px;">No Img</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold"><?= e($item['name']) ?></div>
                                    <?php if ($item['type'] === 'bundle'): ?>
                                        <span class="badge bg-info bg-opacity-10 text-info smaller">Bundle</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary"><?= e($item['category']) ?></span></td>
                                <td style="min-width: 140px;">
                                    <div class="barcode-container text-center">
                                        <svg class="barcode" 
                                             data-value="<?= e($item['sku']) ?>" 
                                             id="barcode-<?= $item['id'] ?>"></svg>
                                        <div class="text-muted smaller" style="font-size: 0.7rem;"><?= e($item['sku']) ?></div>
                                    </div>
                                </td>
                                <td><?= e($item['location']) ?></td>
                                <td class="text-end fw-bold text-primary">₵<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <?php if ($item['quantity'] <= 5): ?>
                                        <span class="badge bg-danger"><?= $item['quantity'] ?> <?= e($item['unit']) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?= $item['quantity'] ?> <?= e($item['unit']) ?></span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <?php if ($item['type'] === 'bundle'): ?>
                                            <a href="<?= BASE_URL ?>/items/preview?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-info" title="Print Preview">
                                                <span class="material-symbols-outlined" style="font-size: 16px;">print</span>
                                            </a>
                                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                            <a href="<?= BASE_URL ?>/items/create-bundle?duplicate_from=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary" title="Duplicate Bundle">
                                                <span class="material-symbols-outlined" style="font-size: 16px;">content_copy</span>
                                            </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if ($_SESSION['role'] === 'admin'): ?>
                                        <a href="<?= BASE_URL ?>/items/edit?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">edit</span>
                                        </a>
                                        <form action="<?= BASE_URL ?>/items/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this item? This will hide it from the list but preserve sales history.')" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <span class="material-symbols-outlined" style="font-size: 16px;">delete</span>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <span class="material-symbols-outlined d-block mb-2" style="font-size: 48px;">inventory_2</span>
                                    No items found <?= ($lowStock ?? false) ? 'with low stock' : '' ?> matching your criteria.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php 
                                $queryParams = $_GET; 
                            ?>
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <?php 
                                    $prevParams = $queryParams;
                                    $prevParams['page'] = $page - 1;
                                ?>
                                <a class="page-link" href="?<?= http_build_query($prevParams) ?>">Previous</a>
                            </li>

                            <?php
                            $range = 2;
                            for ($i = 1; $i <= $totalPages; $i++):
                                if ($i == 1 || $i == $totalPages || ($i >= $page - $range && $i <= $page + $range)):
                                    $pageParams = $queryParams;
                                    $pageParams['page'] = $i;
                            ?>
                                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                        <a class="page-link" href="?<?= http_build_query($pageParams) ?>"><?= $i ?></a>
                                    </li>
                            <?php 
                                elseif ($i == $page - $range - 1 || $i == $page + $range + 1):
                            ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php
                                endif;
                            endfor;
                            ?>

                            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                <?php 
                                    $nextParams = $queryParams;
                                    $nextParams['page'] = $page + 1;
                                ?>
                                <a class="page-link" href="?<?= http_build_query($nextParams) ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    renderBarcodes();
});

function renderBarcodes() {
    const bars = document.querySelectorAll('.barcode');
    bars.forEach(bar => {
        const sku = bar.dataset.value;
        if (sku) {
            JsBarcode(bar, sku, {
                format: "CODE128",
                width: 1.2,
                height: 30,
                displayValue: false,
                margin: 0,
                background: "transparent"
            });
        }
    });
}
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
