<?php
$title = "Edit Item";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
            <h1 class="h2">Edit Item: <?php echo htmlspecialchars($item['name']); ?></h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="<?= BASE_URL ?>/items" class="btn btn-sm btn-outline-secondary">Back to List</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form action="<?= BASE_URL ?>/items/edit?id=<?php echo $item['id']; ?>" method="POST" enctype="multipart/form-data">
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
                            </div>

                            <div class="mb-3 p-4">
                                <label class="form-label fw-bold">Item Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this)">
                                
                                <div class="mt-2 text-center bg-light p-2 rounded" style="min-height: 100px;">
                                    <?php if (!empty($item['image_path'])): ?>
                                        <img id="imagePreview" src="<?= BASE_URL ?>/<?= $item['image_path'] ?>" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px;">
                                    <?php else: ?>
                                        <img id="imagePreview" src="#" alt="Preview" style="max-width: 200px; max-height: 200px; display: none; border-radius: 8px;">
                                        <span id="noImageText" class="text-muted small">No image uploaded</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-3 mb-4 p-4">
                                <a href="<?= BASE_URL ?>/items" class="btn btn-outline-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4">Update Item</button>
                            </div>
                        </form>

                        <script>
                        function previewImage(input) {
                            if (input.files && input.files[0]) {
                                var reader = new FileReader();
                                reader.onload = function(e) {
                                    var img = document.getElementById('imagePreview');
                                    img.src = e.target.result;
                                    img.style.display = 'block';
                                    var noText = document.getElementById('noImageText');
                                    if(noText) noText.style.display = 'none';
                                }
                                reader.readAsDataURL(input.files[0]);
                            }
                        }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    $content = ob_get_clean();
    require __DIR__ . '/../layouts/main.php';
?>
