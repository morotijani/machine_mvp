<?php
$title = "Create Bundle";
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
    <h1 class="h2">Create Bundle / Group Item</h1>
    <a href="<?= BASE_URL ?>/items" class="btn btn-outline-secondary">Cancel</a>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form action="<?= BASE_URL ?>/items/create-bundle" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
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
                        <input type="text" name="name" class="form-control" value="<?= isset($duplicateItem) ? 'Copy of ' . e($duplicateItem['name']) : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control" list="categories" value="<?= isset($duplicateItem) ? e($duplicateItem['category']) : 'Bundles' ?>" required>
                        <datalist id="categories">
                            <option value="Bundles">
                            <option value="Sets">
                            <option value="Spare parts">
                            <option value="Tools">
                            <option value="Mining">
                            <option value="Machine">
                        </datalist>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control" value="<?= isset($duplicateItem) ? strtoupper(substr(uniqid('BND'), 0, 10)) : '' ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Location (Shelf)</label>
                            <input type="text" name="location" class="form-control" placeholder="Shelf A" value="<?= isset($duplicateItem) ? e($duplicateItem['location']) : '' ?>">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Bundle Price (Selling)</label>
                            <div class="input-group">
                                <span class="input-group-text">₵</span>
                                <input type="number" name="price" id="bundle-price" step="0.01" class="form-control" required placeholder="0.00" value="<?= isset($duplicateItem) ? $duplicateItem['price'] : '' ?>">
                            </div>
                            <div class="form-text text-muted">Auto-calculated from items (editable)</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Total Cost Price</label>
                            <div class="input-group">
                                <span class="input-group-text">₵</span>
                                <input type="number" name="cost_price" id="bundle-cost" step="0.01" class="form-control" readonly placeholder="0.00" value="<?= isset($duplicateItem) ? $duplicateItem['cost_price'] : '' ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-12">
                            <label class="form-label">Stock to Create (Pack Quantity)</label>
                            <input type="number" name="quantity" class="form-control" required min="1" value="1">
                            <div class="form-text">How many bundles to pack? This purely multiplies stock deduction.</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
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
                                    <th style="width: 15%;">Stock</th>
                                    <th style="width: 15%;">Selling Price</th>
                                    <th style="width: 10%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($duplicateComponents)): ?>
                                    <?php foreach ($duplicateComponents as $comp): ?>
                                    <tr class="component-row">
                                        <td>
                                            <select name="items[]" class="form-select item-select" required>
                                                <option value="" data-price="0" data-cost="0">Select Item...</option>
                                                <?php foreach ($items as $item): ?>
                                                    <?php if($item['type'] !== 'bundle'): ?>
                                                    <option value="<?= $item['id'] ?>" 
                                                        data-stock="<?= $item['quantity'] ?>" 
                                                        data-price="<?= $item['price'] ?>"
                                                        data-cost="<?= $item['cost_price'] ?>"
                                                        <?= ($comp['child_item_id'] == $item['id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($item['name']) ?> (<?= $item['sku'] ?>)
                                                    </option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="quantities[]" class="form-control qty-input" min="1" value="<?= $comp['quantity'] ?>" required>
                                        </td>
                                        <td><span class="badge bg-secondary current-stock">-</span></td>
                                        <td><span class="text-dark item-price">-</span></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm text-danger remove-row">
                                                <span class="material-symbols-outlined">delete</span>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr class="component-row">
                                        <td>
                                            <select name="items[]" class="form-select item-select" required>
                                                <option value="" data-price="0" data-cost="0">Select Item...</option>
                                                <?php foreach ($items as $item): ?>
                                                    <?php if($item['type'] !== 'bundle'): ?>
                                                    <option value="<?= $item['id'] ?>" 
                                                        data-stock="<?= $item['quantity'] ?>" 
                                                        data-price="<?= $item['price'] ?>"
                                                        data-cost="<?= $item['cost_price'] ?>">
                                                        <?= htmlspecialchars($item['name']) ?> (<?= $item['sku'] ?>)
                                                    </option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="quantities[]" class="form-control qty-input" min="1" value="1" required>
                                        </td>
                                        <td><span class="badge bg-secondary current-stock">-</span></td>
                                        <td><span class="text-dark item-price">-</span></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm text-danger remove-row">
                                                <span class="material-symbols-outlined">delete</span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 bg-light border-top">
                        <p class="mb-0 text-muted small">
                            <span class="material-symbols-outlined align-middle fs-6">info</span>
                            Creating this bundle will <strong>deduct</strong> stock from the selected items immediately.
                        </p>
                    </div>
                </div>
                <div class="card-footer bg-white text-end">
                     <button type="submit" class="btn btn-primary px-4">Create Bundle</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('#components-table tbody');
    const addItemBtn = document.getElementById('add-item-btn');
    const bundlePriceInput = document.getElementById('bundle-price');
    const bundleCostInput = document.getElementById('bundle-cost');
    const bundleQuantityInput = document.querySelector('input[name="quantity"]');
    const submitBtn = document.querySelector('button[type="submit"]');

    function calculateTotals() {
        let totalSelling = 0;
        let totalCost = 0;
        let hasSelection = false;
        let isValidStock = true;
        
        const bundleQty = parseInt(bundleQuantityInput.value) || 0;

        tableBody.querySelectorAll('.component-row').forEach(row => {
            const select = row.querySelector('.item-select');
            const qtyInput = row.querySelector('.qty-input');
            const stockBadge = row.querySelector('.current-stock');
            const priceSpan = row.querySelector('.item-price');
            
            const selectedOption = select.options[select.selectedIndex];
            
            // Reset styles for current row
            qtyInput.classList.remove('is-invalid');
            stockBadge.classList.remove('bg-danger');
            stockBadge.classList.add('bg-secondary');

            if (selectedOption && selectedOption.value) {
                hasSelection = true;
                const price = parseFloat(selectedOption.dataset.price) || 0;
                const cost = parseFloat(selectedOption.dataset.cost) || 0;
                const stock = parseInt(selectedOption.dataset.stock) || 0;
                const qty = parseInt(qtyInput.value) || 0;

                totalSelling += price * qty;
                totalCost += cost * qty;
                
                // Update Price Display
                priceSpan.textContent = '₵' + price.toFixed(2);
                
                // Validate Stock
                const required = qty * bundleQty;
                if (required > stock) {
                    isValidStock = false;
                    qtyInput.classList.add('is-invalid');
                    stockBadge.classList.remove('bg-secondary');
                    stockBadge.classList.add('bg-danger');
                    stockBadge.textContent = `${stock} (Need ${required})`;
                } else {
                    stockBadge.textContent = `${stock} (Use ${required})`;
                }
            } else {
                stockBadge.textContent = '-';
                priceSpan.textContent = '-';
            }
        });

        if (hasSelection) {
            bundlePriceInput.value = totalSelling.toFixed(2);
            bundleCostInput.value = totalCost.toFixed(2);
        } else {
            bundlePriceInput.value = '0.00';
            bundleCostInput.value = '0.00';
        }
        
        if (!isValidStock) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Insufficient Stock';
        } else {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Create Bundle';
        }
    }

    // Initial calculation on page load
    calculateTotals();

    tableBody.addEventListener('change', function(e) {
        if (e.target.classList.contains('item-select')) {
            calculateTotals();
        }
    });

    tableBody.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input')) {
            calculateTotals();
        }
    });
    
    bundleQuantityInput.addEventListener('input', calculateTotals);

    tableBody.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            const row = e.target.closest('tr');
            if (tableBody.querySelectorAll('.component-row').length > 1) {
                row.remove();
                calculateTotals();
            } else {
                // Clear inputs if it's the last row
                row.querySelector('select').value = '';
                row.querySelector('input').value = '1';
                row.querySelector('.item-price').textContent = '-';
                row.querySelector('.current-stock').textContent = '-';
                row.querySelector('.current-stock').classList.remove('bg-danger');
                row.querySelector('.current-stock').classList.add('bg-secondary');
                row.querySelector('.qty-input').classList.remove('is-invalid');
                calculateTotals();
            }
        }
    });

    addItemBtn.addEventListener('click', function() {
        const rowToClone = tableBody.querySelector('.component-row');
        const newRow = rowToClone.cloneNode(true);
        newRow.querySelector('select').value = '';
        newRow.querySelector('input').value = '1';
        newRow.querySelector('.current-stock').textContent = '-';
        newRow.querySelector('.item-price').textContent = '-';
        newRow.querySelector('.current-stock').classList.remove('bg-danger');
        newRow.querySelector('.current-stock').classList.add('bg-secondary');
        newRow.querySelector('.qty-input').classList.remove('is-invalid');
        tableBody.appendChild(newRow);
        calculateTotals(); // Recalculate totals after adding a new row
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
