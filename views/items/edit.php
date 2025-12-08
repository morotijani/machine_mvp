<?php
$title = "Edit Item";
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Item: <?php echo htmlspecialchars($item['name']); ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= BASE_URL ?>/items" class="btn btn-sm btn-outline-secondary">Back to List</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?= BASE_URL ?>/items/edit?id=<?php echo $item['id']; ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Item Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($item['name']); ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Category</label>
                            <input type="text" name="category" class="form-control" list="categories" value="<?php echo htmlspecialchars($item['category']); ?>">
                            <datalist id="categories">
                                <option value="changfan">
                                <option value="mining">
                                <option value="spare parts">
                            </datalist>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">SKU / Code</label>
                            <input type="text" name="sku" class="form-control" value="<?php echo htmlspecialchars($item['sku']); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Selling Price</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="<?php echo htmlspecialchars($item['price']); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Cost Price</label>
                            <input type="number" step="0.01" name="cost_price" class="form-control" value="<?php echo htmlspecialchars($item['cost_price']); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Current Quantity</label>
                            <input type="number" name="quantity" class="form-control" value="<?php echo htmlspecialchars($item['quantity']); ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Unit</label>
                            <input type="text" name="unit" class="form-control" value="<?php echo htmlspecialchars($item['unit']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Location</label>
                            <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($item['location']); ?>" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary px-4">Update Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
