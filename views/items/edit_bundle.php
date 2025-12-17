<?php
$title = "Edit Bundle: " . htmlspecialchars($item['name']);
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
    <h1 class="h2">Edit Bundle</h1>
    <div>
        <button type="button" class="btn btn-outline-warning me-2" data-bs-toggle="modal" data-bs-target="#ungroupModal">
            <span class="material-symbols-outlined align-middle" style="font-size: 18px;">call_split</span> Ungroup
        </button>
        <a href="<?= BASE_URL ?>/items" class="btn btn-outline-secondary">Cancel</a>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form action="<?= BASE_URL ?>/items/edit?id=<?= $item['id'] ?>" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
    <div class="row">
        <!-- Bundle Details -->
        <div class="col-md-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Bundle Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Bundle Name</label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($item['name']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control" list="categories" required value="<?= htmlspecialchars($item['category']) ?>">
                        <datalist id="categories">
                            <option value="Bundles">
                            <option value="Sets">
                            <option value="Promotions">
                        </datalist>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control" placeholder="Leave empty to auto-generate" value="<?= htmlspecialchars($item['sku']) ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($item['location']) ?>">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Bundle Price (Selling)</label>
                            <div class="input-group">
                                <span class="input-group-text">₵</span>
                                <input type="number" name="price" id="bundle-price" step="0.01" class="form-control" required value="<?= $item['price'] ?>">
                            </div>
                        </div>
                        <div class="col-6">
                           <label class="form-label">Bundle Stock (Total)</label>
                           <input type="number" name="quantity" class="form-control" value="<?= $item['quantity'] ?>" min="0">
                           <div class="form-text">Adjusting limits available stock of sub-items.</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                         <label class="form-label">Image</label>
                         <?php if (!empty($item['image_path'])): ?>
                            <div class="mb-2">
                                <img src="<?= BASE_URL ?>/<?= $item['image_path'] ?>" alt="Current Image" class="img-thumbnail" style="max-height: 100px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>
        </div>

        <!-- Bundle Components -->
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Bundle Components</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-item-btn">
                        <span class="material-symbols-outlined align-middle">add</span> Add Item
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0" id="components-table">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Item</th>
                                    <th style="width: 20%;">Qty per Bundle</th>
                                    <th style="width: 20%;">Available Stock</th>
                                    <th style="width: 10%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($components as $comp): ?>
                                <tr class="component-row" data-original-id="<?= $comp['child_item_id'] ?>" data-original-qty="<?= $comp['quantity'] ?>">
                                    <td>
                                        <select name="items[]" class="form-select item-select" required>
                                            <option value="">Select Item...</option>
                                            <?php foreach ($allItems as $itm): ?>
                                                <?php if($itm['type'] !== 'bundle'): ?>
                                                <option value="<?= $itm['id'] ?>" 
                                                    data-stock="<?= $itm['quantity'] ?>" 
                                                    data-price="<?= $itm['price'] ?>"
                                                    data-cost="<?= $itm['cost_price'] ?>"
                                                    <?= ($itm['id'] == $comp['child_item_id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($itm['name']) ?> (<?= $itm['sku'] ?>)
                                                </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="quantities[]" class="form-control qty-input" min="1" value="<?= $comp['quantity'] ?>" required>
                                    </td>
                                    <td>
                                        <!-- Stock Logic is complex here because we already use some of it! -->
                                        <!-- We display Current Stock + (OriginalQty * NetBundleQtyDifference?) -->
                                        <!-- Simplified: Just show spare stock of item. Validation handles logic. -->
                                        <span class="badge bg-secondary current-stock">-</span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm text-danger remove-row">
                                            <span class="material-symbols-outlined">delete</span>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                     <div class="p-3 bg-light border-top">
                        <p class="mb-0 text-muted small">
                            <span class="material-symbols-outlined align-middle fs-6">warning</span>
                            <strong>Warning:</strong> Changing quantity or composition checks "Net Stock". 
                            If you increase usage, it deducts from spare items. 
                            If you decrease usage, it returns items to stock.
                        </p>
                    </div>
                </div>
                <div class="card-footer bg-white text-end">
                     <button type="submit" class="btn btn-primary px-4">Update Bundle & Stock</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Ungroup Modal -->
<div class="modal fade" id="ungroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= BASE_URL ?>/items/ungroup-bundle" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ungroup Bundle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>This will disassemble the bundle and return items to stock.</p>
                <div class="mb-3">
                    <label class="form-label">How many bundles to ungroup?</label>
                    <input type="number" name="quantity" class="form-control" min="1" max="<?= $item['quantity'] ?>" value="1" required>
                    <div class="form-text">Available: <?= $item['quantity'] ?></div>
                </div>
                <input type="hidden" name="bundle_id" value="<?= $item['id'] ?>">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning">Ungroup & Restore Stock</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('#components-table tbody');
    const addItemBtn = document.getElementById('add-item-btn');
    // Store initial Bundle Qty
    const initialBundleQty = <?= $item['quantity'] ?>;
    
    // We need a template since PHP loop might be empty or full
    // Let's create a hidden template row from the first item if exists, or hardcode it
    // Hardcoding template creation in JS is cleaner
    // But we need the item options. 
    // Let's use the first row if exists, else we need the data from PHP.
    // Easier hack: Always render a hidden template row? Or just clone the first one if present.
    // Issue: If verified fully working, we can just use the rendered rows.
  
    function updateStockDisplay(row) {
        const select = row.querySelector('.item-select');
        const stockBadge = row.querySelector('.current-stock');
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            stockBadge.textContent = selectedOption.dataset.stock;
        } else {
            stockBadge.textContent = '-';
        }
    }

     // Initial update of all rows
    document.querySelectorAll('.component-row').forEach(row => {
        updateStockDisplay(row);
    });

    const bundleQtyInput = document.querySelector('input[name="quantity"]');
    bundleQtyInput.addEventListener('input', validateStock);

    function validateStock() {
        const newBundleQty = parseInt(bundleQtyInput.value) || 0;
        const rows = tableBody.querySelectorAll('.component-row');
        let isValid = true;
        const submitBtn = document.querySelector('button[type="submit"]');

        rows.forEach(row => {
            const select = row.querySelector('.item-select');
            const qtyInput = row.querySelector('.qty-input');
            const stockBadge = row.querySelector('.current-stock');
            const selectedOption = select.options[select.selectedIndex];
            
            // Get original data from row attribute if it exists (items might change!)
            // Strategy:
            // 1. Calculate how many of THIS item we relied on before.
            //    We need to look up if this item ID was in our "original" map.
            //    But storing original map in JS is needed.
            // Let's rely on the fact that stockBadge has "Current Spare Stock".
            // So: Available = CurrentSpare + (OriginalUsed)
            // Needed = NewBundleQty * NewRecipeQty
            
            // Wait, selectedOption.dataset.stock IS the database's current 'quantity'.
            // If the item was part of the bundle before, that 'quantity' does NOT include the ones locked in the bundle.
            // So we can assume `dataset.stock` is purely "Spare on Shelf".
            
            // However, if we simply edit the quantity of the bundle, we might be freeing up stock or using more.
            // Correct Logic:
            // Net Change = (NewBundleQty * NewRecipeQty) - (OldBundleQty * OldRecipeQty)
            // If NetChange > 0: We need that many MORE. Check if (Spare >= NetChange).
            // If NetChange <= 0: We are safe.
            
            // To do this, we need to know OldRecipeQty for this specific Item ID.
            // We can parse the initial PHP render to build a map.
            
            // Reset UI
            qtyInput.classList.remove('is-invalid');
            stockBadge.classList.remove('bg-danger');
            stockBadge.classList.add('bg-secondary');

            if (selectedOption && selectedOption.value) {
                const itemId = selectedOption.value;
                const newRecipeQty = parseInt(qtyInput.value) || 0;
                
                // Lookup old recipe qty
                let oldRecipeQty = 0;
                // This lookup is tricky because the user might change the SELECT to a different item.
                // We need a global map of {itemId: originalRecipeQty} generated from server side.
                if (window.originalRecipe && window.originalRecipe[itemId]) {
                    oldRecipeQty = window.originalRecipe[itemId];
                }

                if (newRecipeQty <= 0) {
                    isValid = false;
                    qtyInput.classList.add('is-invalid');
                    submitBtn.textContent = "Invalid Quantity";
                }
                
                const oldTotalUsed = initialBundleQty * oldRecipeQty;
                const newTotalNeeded = newBundleQty * newRecipeQty;
                
                const netChange = newTotalNeeded - oldTotalUsed;
                const spareStock = parseInt(selectedOption.dataset.stock) || 0;

                if (netChange > 0) {
                    // We need more stock than we used to use.
                    // Do we have enough spare?
                    if (spareStock < netChange) {
                        isValid = false;
                        qtyInput.classList.add('is-invalid');
                        stockBadge.classList.remove('bg-secondary');
                        stockBadge.classList.add('bg-danger');
                        stockBadge.textContent = `${spareStock} (Need +${netChange})`;
                    } else {
                        stockBadge.textContent = `${spareStock} (Using +${netChange})`;
                    }
                } else {
                    // netChange <= 0 means we are freeing up stock or using same. Safe.
                    const freeing = Math.abs(netChange);
                    stockBadge.textContent = `${spareStock} (Freeing ${freeing})`;
                }
            }
        });

        if (!isValid) {
            submitBtn.disabled = true;
            submitBtn.textContent = "Insufficient Stock";
        } else {
            submitBtn.disabled = false;
            submitBtn.textContent = "Update Bundle & Stock";
        }
    }

    // Event delegation
    tableBody.addEventListener('change', function(e) {
        if (e.target.classList.contains('item-select')) {
            updateStockDisplay(e.target.closest('tr'));
            validateStock();
        }
    });

    tableBody.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input')) {
            validateStock();
        }
    });

    tableBody.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            const row = e.target.closest('tr');
            if (tableBody.querySelectorAll('.component-row').length > 1) {
                row.remove();
                validateStock();
            } else {
                row.querySelector('select').value = '';
                row.querySelector('input').value = '1';
                updateStockDisplay(row);
            }
        }
    });

    addItemBtn.addEventListener('click', function() {
        // Clone the first row found (assuming at least one exists)
        // If list is empty (rare), we'd need a create element logic.
        const rowToClone = tableBody.querySelector('.component-row');
        const newRow = rowToClone.cloneNode(true);
        newRow.querySelector('select').value = '';
        newRow.querySelector('input').value = '1';
        newRow.querySelector('.current-stock').textContent = '-';
        newRow.removeAttribute('data-original-id'); // Clear tracking
        newRow.removeAttribute('data-original-qty');
        tableBody.appendChild(newRow);
    });

});

// Build Original Recipe Map for JS
window.originalRecipe = {};
<?php foreach ($components as $comp): ?>
window.originalRecipe[<?= $comp['child_item_id'] ?>] = <?= $comp['quantity'] ?>;
<?php endforeach; ?>
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
