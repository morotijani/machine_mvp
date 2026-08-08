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

        .no-print,
        .btn,
        .navbar,
        .sidebar,
        form.d-flex,
        .pagination,
        .page-header-actions {
            display: none !important;
        }

        .card {
            border: none !important;
            shadow: none !important;
        }

        .table-responsive {
            overflow: visible !important;
        }

        body {
            background-color: #fff !important;
        }

        main {
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Hide actions column in print */
        th:last-child,
        td:last-child {
            display: none !important;
        }

        .badge {
            border: 1px solid #000;
            color: #000 !important;
            background: #fff !important;
        }

        /* Ensure all rows show */
        tr {
            page-break-inside: avoid;
        }
    }

    .google-search-wrapper {
        background: #fff;
        border-radius: 24px;
        padding: 4px 8px;
        display: flex;
        align-items: center;
        box-shadow: 0 1px 2px 0 rgba(60, 64, 67, 0.3), 0 1px 3px 1px rgba(60, 64, 67, 0.15);
        max-width: 800px;
        width: 100%;
    }

    .google-search-input {
        border: none;
        outline: none;
        padding: 8px 12px;
        flex-grow: 1;
        font-size: 15px;
    }

    .google-search-select {
        border: none;
        outline: none;
        padding: 8px 12px;
        color: #5f6368;
        background: transparent;
        font-size: 14px;
        cursor: pointer;
    }

    .google-divider {
        width: 1px;
        height: 24px;
        background-color: #dadce0;
        margin: 0 8px;
    }

    .google-btn-primary {
        background-color: #0b57d0;
        color: #fff;
        border-radius: 20px;
        padding: 8px 24px;
        font-weight: 500;
        border: none;
        transition: background-color 0.2s;
        text-decoration: none;
    }

    .google-btn-primary:hover {
        background-color: #0842a0;
        color: #fff;
    }

    .google-btn-secondary {
        background-color: transparent;
        color: #444746;
        border-radius: 20px;
        padding: 8px 16px;
        font-weight: 500;
        border: 1px solid #747775;
        transition: background-color 0.2s;
        text-decoration: none;
    }

    .google-btn-secondary:hover {
        background-color: #f1f3f4;
        color: #1f1f1f;
    }

    .google-table-card {
        background: #fff;
        border-radius: 24px;
        overflow: hidden;
        border: none;
        margin-bottom: 30px;
    }

    .google-table-card thead th {
        border-bottom: 1px solid #e3e3e3;
        background-color: #fff;
        color: #5f6368;
        font-weight: 500;
        padding: 16px 24px;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .google-table-card tbody td {
        padding: 16px 24px;
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

    .action-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        border: none;
        background: transparent;
        transition: background-color 0.2s;
        text-decoration: none;
    }

    .action-btn:hover {
        background-color: #f1f3f4;
        color: #202124;
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

    .google-alert {
        border-radius: 16px;
        padding: 12px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        font-size: 14px;
        font-weight: 500;
    }

    .google-alert-danger {
        background-color: #fce8e6;
        color: #c5221f;
        border: 1px solid #fad2cf;
    }

    .google-alert-success {
        background-color: #e6f4ea;
        color: #137333;
        border: 1px solid #ceead6;
    }

    @media (max-width: 768px) {
        .google-search-wrapper {
            flex-wrap: wrap;
            border-radius: 16px;
            padding: 12px;
            gap: 8px;
        }

        .google-search-input {
            width: 100%;
            border-bottom: 1px solid #e3e3e3;
            padding-bottom: 12px;
            margin-bottom: 4px;
            flex-grow: 1;
        }

        .google-divider {
            display: none;
        }

        .google-search-select {
            flex-grow: 1;
            background: #f1f3f4;
            border-radius: 8px;
            padding: 8px;
            width: calc(50% - 4px);
        }

        .google-search-wrapper label {
            width: 100%;
            justify-content: center;
            background: #f1f3f4;
            padding: 8px;
            border-radius: 8px;
            margin-top: 4px;
            margin-right: 0 !important;
        }
    }
</style>

<div class="row justify-content-center">
    <div class="col-12 col-xxl-11">
        <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 gap-2">
            <!-- <h1 class="h2 mb-0">Items & Machines</h1> -->
            <h1 class="h2 mb-0">Items</h1>
            <div
                class="d-flex flex-wrap gap-2 align-items-center justify-content-start justify-content-md-end flex-grow-1">
                <form action="" method="GET" class="d-flex flex-grow-1 flex-md-grow-0 gap-2" style="min-width: 280px;">
                    <div class="google-search-wrapper flex-grow-1" style="max-width: none;">
                        <span class="material-symbols-outlined text-muted" style="margin-left: 8px;">search</span>
                        <input type="text" name="search" class="google-search-input" placeholder="Search items..."
                            value="<?= e($search ?? '') ?>">

                        <div class="google-divider"></div>
                        <select name="sort" class="google-search-select" onchange="this.form.submit()">
                            <option value="name" <?= ($sort == 'name') ? 'selected' : '' ?>>Name</option>
                            <option value="price" <?= ($sort == 'price') ? 'selected' : '' ?>>Price</option>
                            <option value="quantity" <?= ($sort == 'quantity') ? 'selected' : '' ?>>Stock Level</option>
                            <option value="created_at" <?= ($sort == 'created_at') ? 'selected' : '' ?>>Date Added</option>
                        </select>

                        <div class="google-divider"></div>
                        <select name="order" class="google-search-select" onchange="this.form.submit()">
                            <option value="ASC" <?= ($order == 'ASC') ? 'selected' : '' ?>>ASC</option>
                            <option value="DESC" <?= ($order == 'DESC') ? 'selected' : '' ?>>DESC</option>
                        </select>

                        <div class="google-divider"></div>
                        <label class="d-flex align-items-center gap-1 mb-0"
                            style="cursor: pointer; margin-right: 12px;">
                            <input type="checkbox" name="low_stock" value="1" <?= ($lowStock ?? false) ? 'checked' : '' ?> onchange="this.form.submit()" style="accent-color: #c5221f;">
                            <span
                                class="material-symbols-outlined <?= ($lowStock ?? false) ? 'text-danger' : 'text-muted' ?>"
                                style="font-size: 18px;">warning</span>
                            <span style="font-size: 14px; color: #5f6368; font-weight: 500;">Low Stock</span>
                        </label>
                    </div>

                    <?php if (!empty($search) || ($lowStock ?? false) || $sort !== 'created_at' || $order !== 'DESC'): ?>
                        <a href="<?= BASE_URL ?>/items" class="google-btn-secondary d-flex align-items-center px-3"
                            style="white-space: nowrap;">Clear</a>
                    <?php endif; ?>
                </form>

                <div class="d-flex flex-wrap gap-2 page-header-actions">
                    <?php
                    // Preserve current search/sort params for print link
                    $printParams = $_GET;
                    $printParams['print'] = 1;
                    $printParams['page'] = 1;
                    ?>
                    <?php if (isset($isPrint) && $isPrint): ?>
                        <a href="<?= BASE_URL ?>/items" class="google-btn-secondary d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined" style="font-size: 18px;">arrow_back</span> Back
                        </a>
                    <?php else: ?>
                        <a href="?<?= http_build_query($printParams) ?>"
                            class="google-btn-secondary d-flex align-items-center gap-2" target="_blank">
                            <span class="material-symbols-outlined" style="font-size: 18px;">print</span> Print
                        </a>
                    <?php endif; ?>

                    <?php if ($_SESSION['role'] === 'admin' && (!isset($isPrint) || !$isPrint)): ?>
                        <a href="<?= BASE_URL ?>/items/create-bundle"
                            class="google-btn-secondary d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined" style="font-size: 18px;">inventory_2</span> New Bundle
                        </a>
                        <a href="<?= BASE_URL ?>/items/create" class="google-btn-primary d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined" style="font-size: 18px;">add</span> New Item
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="google-alert google-alert-success alert-dismissible fade show">
                <span class="material-symbols-outlined">check_circle</span>
                <div class="flex-grow-1">
                    <?= htmlspecialchars($_GET['success']) ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                    style="font-size: 12px; padding: 1.25rem;"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="google-alert google-alert-danger alert-dismissible fade show">
                <span class="material-symbols-outlined">error</span>
                <div class="flex-grow-1">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                    style="font-size: 12px; padding: 1.25rem;"></button>
            </div>
        <?php endif; ?>

        <div class="google-table-card">
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 60px;">Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th class="d-none">SKU / Barcode</th>
                            <th>Location</th>
                            <th>Added By</th>
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
                                        <img src="<?= BASE_URL ?>/<?php echo $item['image_path']; ?>" class="rounded"
                                            style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted small"
                                            style="width: 40px; height: 40px;">No Img</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/items/view?id=<?= $item['id'] ?>" class="text-decoration-none">
                                        <div class="fw-bold"><?= e($item['name']) ?></div>
                                    </a>
                                    <?php if ($item['type'] === 'bundle'): ?>
                                        <span class="badge bg-info bg-opacity-10 text-info smaller">Bundle</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="google-badge"
                                        style="background-color: #f1f3f4; color: #444746;"><?= e($item['category']) ?></span>
                                </td>
                                <td class="d-none" style="min-width: 140px;">
                                    <div class="barcode-container text-center">
                                        <svg class="barcode" data-value="<?= e($item['sku']) ?>"
                                            id="barcode-<?= $item['id'] ?>"></svg>
                                        <div class="text-muted smaller" style="font-size: 0.7rem;"><?= e($item['sku']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?= e($item['location']) ?></td>
                                <td class="text-muted" style="font-size: 0.85rem;">
                                    <span class="material-symbols-outlined align-middle" style="font-size: 14px;">person</span>
                                    <?= e($item['created_by_name'] ?? 'System') ?>
                                </td>
                                <td class="text-end fw-bold text-primary">₵<?php echo number_format($item['price'], 2); ?>
                                </td>
                                <td>
                                    <?php if ($item['quantity'] <= 5): ?>
                                        <span class="google-badge badge-subtle-danger"><?= $item['quantity'] ?>
                                            <?= e($item['unit']) ?></span>
                                    <?php else: ?>
                                        <span class="google-badge badge-subtle-success"><?= $item['quantity'] ?>
                                            <?= e($item['unit']) ?></span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-end text-nowrap">
                                    <?php if ($item['type'] === 'bundle'): ?>
                                        <a href="<?= BASE_URL ?>/items/preview?id=<?php echo $item['id']; ?>"
                                            class="action-btn text-info" title="Print Preview">
                                            <span class="material-symbols-outlined" style="font-size: 20px;">print</span>
                                        </a>
                                        <?php if ($_SESSION['role'] === 'admin'): ?>
                                            <a href="<?= BASE_URL ?>/items/create-bundle?duplicate_from=<?php echo $item['id']; ?>"
                                                class="action-btn text-primary" title="Duplicate Bundle">
                                                <span class="material-symbols-outlined" style="font-size: 20px;">content_copy</span>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if ($_SESSION['role'] === 'admin'): ?>
                                        <a href="<?= BASE_URL ?>/items/edit?id=<?php echo $item['id']; ?>"
                                            class="action-btn text-primary" title="Edit">
                                            <span class="material-symbols-outlined" style="font-size: 20px;">edit</span>
                                        </a>
                                        <button type="button" class="action-btn text-danger" title="Delete"
                                            data-bs-toggle="modal" data-bs-target="#deleteItemModal"
                                            data-id="<?= $item['id'] ?>" data-name="<?= e($item['name']) ?>">
                                            <span class="material-symbols-outlined" style="font-size: 20px;">delete</span>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <span class="material-symbols-outlined d-block mb-2"
                                        style="font-size: 48px;">inventory_2</span>
                                    No items found <?= ($lowStock ?? false) ? 'with low stock' : '' ?> matching your
                                    criteria.
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

    <!-- Delete Item Modal -->
    <div class="modal fade" id="deleteItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title" style="color: #1f1f1f; font-weight: 500;">Delete Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= BASE_URL ?>/items/delete" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="id" id="delete_id">
                    <div class="modal-body pt-3 pb-4 px-4 text-center">
                        <span class="material-symbols-outlined text-danger mb-3" style="font-size: 48px;">warning</span>
                        <h5 class="mb-2" id="delete_name_display" style="color: #1f1f1f; font-weight: 500;"></h5>
                        <p style="color: #5f6368; font-size: 15px; margin-bottom: 0;">
                            Are you sure you want to delete this item?<br>
                            This will hide it from the list but preserve its sales history.
                        </p>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4 justify-content-center">
                        <button type="button" class="google-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="google-btn-primary" style="background-color: #dc3545;">Delete
                            Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const barcodes = document.querySelectorAll('.barcode');
            barcodes.forEach(function (svg) {
                const value = svg.getAttribute('data-value');
                if (value) {
                    try {
                        JsBarcode(svg, value, {
                            format: "CODE128",
                            lineColor: "#000",
                            width: 1.5,
                            height: 30,
                            displayValue: false,
                            margin: 0
                        });
                    } catch (e) {
                        console.error("Error generating barcode for " + value, e);
                    }
                }
            });

            // Populate Delete Modal
            var deleteItemModal = document.getElementById('deleteItemModal');
            if (deleteItemModal) {
                deleteItemModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var id = button.getAttribute('data-id');
                    var name = button.getAttribute('data-name');

                    var modal = this;
                    modal.querySelector('#delete_id').value = id;
                    modal.querySelector('#delete_name_display').textContent = name;
                });
            }
        });
    </script>
</div>
</div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>