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
                            <option value="Spare parts">
                            <option value="Sets">
                            <option value="Tools">
                            <option value="Mining">
                            <option value="Machine">
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
                            <label class="form-label">Bundle Cost (Calculated)</label>
                            <div class="input-group">
                                <span class="input-group-text">₵</span>
                                <input type="number" name="cost_price" id="bundle-cost" step="0.01" class="form-control" readonly value="<?= $item['cost_price'] ?>">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                         <label class="form-label">Bundle Stock (Total)</label>
                         <input type="number" name="quantity" class="form-control" value="<?= $item['quantity'] ?>" min="0">
                         <div class="form-text">Adjusting limits available stock of sub-items.</div>
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
                                    <th style="width: 40%;">Item</th>
                                    <th style="width: 20%;">Qty per Bundle</th>
                                    <th style="width: 15%;">Available Stock</th>
                                    <th style="width: 15%;">Selling Price</th>
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
                                    <td><span class="text-dark item-price">-</span></td>
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
    const bundleQtyInput = document.querySelector('input[name="quantity"]');
    const submitBtn = document.querySelector('button[type="submit"]');

    // Safe PHP Injection
    const initialBundleQty = <?= (int)($item['quantity'] ?? 0) ?>;
    
    // Build Original Recipe Map
    // Keys are quoted to handle potential non-numeric IDs safely
    const originalRecipe = {};
    <?php foreach ($components as $comp): ?>
    originalRecipe['<?= $comp['child_item_id'] ?>'] = <?= (int)$comp['quantity'] ?>;
    <?php endforeach; ?>

    function updateStockDisplay(row) {
        const select = row.querySelector('.item-select');
        const stockBadge = row.querySelector('.current-stock');
        const priceSpan = row.querySelector('.item-price');
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            stockBadge.textContent = selectedOption.dataset.stock;
            const price = parseFloat(selectedOption.dataset.price) || 0;
            priceSpan.textContent = '₵' + price.toFixed(2);
        } else {
            stockBadge.textContent = '-';
            priceSpan.textContent = '-';
        }
    }

    function validateStock() {
        const newBundleQty = parseInt(bundleQtyInput.value) || 0;
        const rows = tableBody.querySelectorAll('.component-row');
        let isValid = true;
        let rejectReason = "";
        
        // Price Calculation Variables
        let totalSelling = 0;
        let totalCost = 0;
        let hasSelection = false;
        
        const bundlePriceInput = document.getElementById('bundle-price');
        const bundleCostInput = document.getElementById('bundle-cost');

        // Reset UI first
        rows.forEach(row => {
            const qtyInput = row.querySelector('.qty-input');
            const stockBadge = row.querySelector('.current-stock');
            
            qtyInput.classList.remove('is-invalid');
            stockBadge.classList.remove('bg-danger');
            stockBadge.classList.remove('text-white'); // Ensure readability
            stockBadge.classList.add('bg-secondary');
        });

        rows.forEach(row => {
            const select = row.querySelector('.item-select');
            const qtyInput = row.querySelector('.qty-input');
            const stockBadge = row.querySelector('.current-stock');
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption && selectedOption.value) {
                hasSelection = true;
                const itemId = selectedOption.value;
                const newRecipeQty = parseInt(qtyInput.value) || 0;
                
                // Price Math
                const price = parseFloat(selectedOption.dataset.price) || 0;
                const cost = parseFloat(selectedOption.dataset.cost) || 0;
                totalSelling += price * newRecipeQty;
                totalCost += cost * newRecipeQty;
                
                // 1. Strict Recipe Quantity Check
                if (newRecipeQty <= 0) {
                    isValid = false;
                    rejectReason = "Invalid Quantity";
                    qtyInput.classList.add('is-invalid'); // Mark as problematic
                    return; // Skip math for this invalid row
                }
                
                // 2. Net Stock Calculation
                // Lookup old recipe qty
                let oldRecipeQty = 0;
                if (originalRecipe.hasOwnProperty(itemId)) {
                    oldRecipeQty = originalRecipe[itemId];
                }
                
                const oldTotalUsed = initialBundleQty * oldRecipeQty;
                const newTotalNeeded = newBundleQty * newRecipeQty;
                
                const netChange = newTotalNeeded - oldTotalUsed;
                const spareStock = parseInt(selectedOption.dataset.stock) || 0;

                if (netChange > 0) {
                    // We need more stock.
                    if (spareStock < netChange) {
                        isValid = false;
                        rejectReason = "Insufficient Stock";
                        qtyInput.classList.add('is-invalid'); // Mark as problematic
                        stockBadge.classList.remove('bg-secondary');
                        stockBadge.classList.add('bg-danger');
                        stockBadge.classList.add('text-white');
                        stockBadge.textContent = `${spareStock} (Need +${netChange})`;
                    } else {
                        stockBadge.textContent = `${spareStock} (Using +${netChange})`;
                    }
                } else {
                    // Freeing stock or neutral
                    const freeing = Math.abs(netChange);
                    stockBadge.textContent = `${spareStock} (Freeing ${freeing})`;
                }
            }
        });

        // Update Price Inputs
        if (hasSelection) {
            // Only update if user hasn't manually overridden it? 
            // Strict requirement: "automatically calculating". usually implies it overwrites.
            // Let's overwrite for consistency with Create Bundle.
            if(bundlePriceInput) bundlePriceInput.value = totalSelling.toFixed(2);
            if(bundleCostInput) bundleCostInput.value = totalCost.toFixed(2);
        }

        if (newBundleQty < 0) {
             isValid = false;
             rejectReason = "Invalid Bundle Qty";
             bundleQtyInput.classList.add('is-invalid');
        } else {
             bundleQtyInput.classList.remove('is-invalid');
        }

        if (!isValid) {
            submitBtn.disabled = true;
            submitBtn.textContent = rejectReason || "Invalid Input";
        } else {
            submitBtn.disabled = false;
            submitBtn.textContent = "Update Bundle & Stock";
        }
    }

    // Event Listeners
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        // Run validation one last time
        validateStock();
        const submitBtn = document.querySelector('button[type="submit"]');
        if (submitBtn.disabled) {
            e.preventDefault();
            e.stopPropagation();
            alert("Please fix errors before updating.");
            return false;
        }
    });

    bundleQtyInput.addEventListener('input', validateStock);

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
                validateStock();
            }
        }
    });

    addItemBtn.addEventListener('click', function() {
        // Clone the first row found (assuming at least one exists)
        const rowToClone = tableBody.querySelector('.component-row');
        if (rowToClone) {
            const newRow = rowToClone.cloneNode(true);
            newRow.querySelector('select').value = '';
            newRow.querySelector('input').value = '1';
            newRow.querySelector('.current-stock').textContent = '-';
            newRow.querySelector('.item-price').textContent = '-';
            newRow.removeAttribute('data-original-id'); 
            newRow.removeAttribute('data-original-qty');
            
            // Clear Validation Classes on clone
            newRow.querySelector('.qty-input').classList.remove('is-invalid');
            newRow.querySelector('.current-stock').classList.remove('bg-danger');
            newRow.querySelector('.current-stock').classList.add('bg-secondary');

            tableBody.appendChild(newRow);
            validateStock();
        }
    });

    // Initial Run
    document.querySelectorAll('.component-row').forEach(row => {
        updateStockDisplay(row);
    });
    validateStock();
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
