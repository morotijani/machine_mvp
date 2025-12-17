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
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control" list="categories" required>
                        <datalist id="categories">
                            <option value="Bundles">
                            <option value="Sets">
                            <option value="Promotions">
                        </datalist>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Location (Shelf)</label>
                            <input type="text" name="location" class="form-control">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                         <div class="col-6">
                             <label class="form-label">Bundle Price (Selling)</label>
                            <div class="input-group">
                                <span class="input-group-text">₵</span>
                                <input type="number" name="price" id="bundle-price" step="0.01" class="form-control" required placeholder="0.00">
                            </div>
                            <div class="form-text text-muted">Auto-calculated from items (editable)</div>
                         </div>
                         <div class="col-6">
                            <label class="form-label">Total Cost Price</label>
                            <div class="input-group">
                                <span class="input-group-text">₵</span>
                                <input type="number" name="cost_price" id="bundle-cost" step="0.01" class="form-control" readonly placeholder="0.00">
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
                                    <th style="width: 50%;">Item</th>
                                    <th style="width: 20%;">Qty per Bundle</th>
                                    <th style="width: 20%;">Current Stock</th>
                                    <th style="width: 10%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="component-row">
                                    <td>
                                        <select name="items[]" class="form-select item-select" required>
                                            <option value="" data-price="0" data-cost="0">Select Item...</option>
                                            <?php foreach ($items as $item): ?>
                                                <?php if($item['type'] !== 'bundle'): ?>
                                                <option value="<?= $item['id'] ?>" data-stock="<?= $item['quantity'] ?>" data-price="<?= $item['price'] ?>" data-cost="<?= $item['cost_price'] ?>">
                                                    <?= htmlspecialchars($item['name']) ?> (<?= $item['sku'] ?>)
                                                </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="quantities[]" class="form-control qty-input" min="1" value="1" required>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary current-stock">-</span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm text-danger remove-row">
                                            <span class="material-symbols-outlined">delete</span>
                                        </button>
                                    </td>
                                </tr>
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
    const firstRow = tableBody.querySelector('.component-row');
    const bundlePriceInput = document.getElementById('bundle-price');
    const bundleCostInput = document.getElementById('bundle-cost');

    function updateStockDisplay(row) {
        const select = row.querySelector('.item-select');
        const stockBadge = row.querySelector('.current-stock');
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            stockBadge.textContent = selectedOption.dataset.stock;
        } else {
            stockBadge.textContent = '-';
        }
        calculateTotals();
    }

    function calculateTotals() {
        let totalSelling = 0;
        let totalCost = 0;
        const rows = tableBody.querySelectorAll('.component-row');

        rows.forEach(row => {
            const select = row.querySelector('.item-select');
            const qtyInput = row.querySelector('.qty-input');
            const selectedOption = select.options[select.selectedIndex];
            const quantity = parseFloat(qtyInput.value) || 0;

            if (selectedOption && selectedOption.value) {
                const price = parseFloat(selectedOption.dataset.price) || 0;
                const cost = parseFloat(selectedOption.dataset.cost) || 0;
                
                totalSelling += (price * quantity);
                totalCost += (cost * quantity);
            }
        });

        // Update inputs
        bundlePriceInput.value = totalSelling.toFixed(2);
        bundleCostInput.value = totalCost.toFixed(2);
        
        validateStock();
    }

    function validateStock() {
        const bundleQty = parseInt(document.querySelector('input[name="quantity"]').value) || 0;
        const rows = tableBody.querySelectorAll('.component-row');
        let isValid = true;
        const submitBtn = document.querySelector('button[type="submit"]');

        rows.forEach(row => {
            const select = row.querySelector('.item-select');
            const qtyInput = row.querySelector('.qty-input');
            const stockBadge = row.querySelector('.current-stock');
            
            const selectedOption = select.options[select.selectedIndex];
            
            // Reset styles
            qtyInput.classList.remove('is-invalid');
            stockBadge.classList.remove('bg-danger');
            stockBadge.classList.add('bg-secondary');

            if (selectedOption && selectedOption.value) {
                const currentStock = parseInt(selectedOption.dataset.stock) || 0;
                const neededPerBundle = parseInt(qtyInput.value) || 0;
                const totalNeeded = neededPerBundle * bundleQty;

                if (totalNeeded > currentStock) {
                    isValid = false;
                    qtyInput.classList.add('is-invalid');
                    stockBadge.classList.remove('bg-secondary');
                    stockBadge.classList.add('bg-danger');
                    stockBadge.textContent = `${currentStock} (Need ${totalNeeded})`;
                } else {
                     stockBadge.textContent = currentStock;
                }
            }
        });

        if (!isValid) {
            submitBtn.disabled = true;
            submitBtn.textContent = "Insufficient Stock";
        } else {
            submitBtn.disabled = false;
            submitBtn.textContent = "Create Bundle";
        }
    }
    
    document.querySelector('input[name="quantity"]').addEventListener('input', calculateTotals);

    // Event delegation for validation and dynamic rows
    tableBody.addEventListener('change', function(e) {
        if (e.target.classList.contains('item-select')) {
            updateStockDisplay(e.target.closest('tr'));
        }
    });

    tableBody.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input')) {
            calculateTotals();
        }
    });

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
                updateStockDisplay(row);
            }
        }
    });

    addItemBtn.addEventListener('click', function() {
        const newRow = firstRow.cloneNode(true);
        newRow.querySelector('select').value = '';
        newRow.querySelector('input').value = '1';
        newRow.querySelector('.current-stock').textContent = '-';
        tableBody.appendChild(newRow);
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
