<?php
$title = "Add New Item";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3 mb-3">
            <h1 class="h3" style="color: #1f1f1f; font-weight: 400;">Add New Item</h1>
            <a href="<?= $_SESSION['last_items_url'] ?? (BASE_URL . '/items') ?>" class="btn btn-light d-flex align-items-center gap-2 text-decoration-none" style="border-radius: 20px; font-weight: 500;">
                <span class="material-symbols-outlined fs-6">arrow_back</span> Back to List
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 16px; border: none; background-color: #fce8e6; color: #c5221f;">
                <span class="material-symbols-outlined align-middle me-2">error</span>
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <style>
            .google-card {
                background: #fff;
                border-radius: 28px;
                overflow: hidden;
                margin-bottom: 24px;
                border: none;
                box-shadow: none;
            }
            .google-row {
                display: flex;
                align-items: center;
                padding: 24px 32px;
                border-bottom: 1px solid #e3e3e3;
                transition: background-color 0.2s;
            }
            .google-row:last-child {
                border-bottom: none;
            }
            .google-row:focus-within {
                background-color: #f8f9fa;
            }
            .google-icon {
                color: #444746;
                margin-right: 24px;
                font-size: 24px;
            }
            .google-content {
                flex-grow: 1;
                display: flex;
                flex-direction: column;
            }
            .google-label {
                font-size: 16px;
                color: #1f1f1f;
                margin-bottom: 4px;
                font-weight: 500;
            }
            .google-input, .google-textarea, .google-select {
                border: none;
                outline: none;
                background: transparent;
                font-size: 15px;
                color: #5f6368;
                padding: 0;
                width: 100%;
            }
            .google-input:focus, .google-textarea:focus, .google-select:focus {
                color: #202124;
            }
            .google-textarea {
                resize: none;
                margin-top: 2px;
            }
            .btn-save {
                background-color: #0b57d0;
                color: #fff;
                border-radius: 24px;
                padding: 10px 24px;
                font-weight: 500;
                border: none;
                transition: background-color 0.2s;
            }
            .btn-save:hover {
                background-color: #0842a0;
            }
        </style>

        <form action="<?= BASE_URL ?>/items/create" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="google-card">
                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">inventory_2</span>
                    <div class="google-content">
                        <label class="google-label">Item Name</label>
                        <input type="text" name="name" class="google-input" placeholder="e.g. Printer Toner" required>
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">category</span>
                    <div class="google-content">
                        <label class="google-label">Category</label>
                        <input type="text" name="category" class="google-input" list="categories" placeholder="e.g. Spare parts" required>
                        <datalist id="categories">
                            <option value="Spare parts">
                            <option value="Tools">
                            <option value="Mining">
                            <option value="Machine">
                        </datalist>
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">barcode</span>
                    <div class="google-content">
                        <label class="google-label">SKU / Code</label>
                        <input type="text" name="sku" class="google-input" placeholder="Optional identifier">
                    </div>
                </div>
            </div>
            
            <div class="google-card">
                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">sell</span>
                    <div class="google-content">
                        <label class="google-label">Selling Price</label>
                        <input type="number" step="0.01" min="0" name="price" class="google-input" placeholder="0.00" required>
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">payments</span>
                    <div class="google-content">
                        <label class="google-label">Cost Price</label>
                        <input type="number" step="0.01" min="0" name="cost_price" class="google-input" placeholder="0.00" required>
                    </div>
                </div>
            </div>

            <div class="google-card">
                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">format_list_numbered</span>
                    <div class="google-content">
                        <label class="google-label">Initial Quantity</label>
                        <input type="number" name="quantity" class="google-input" value="0" required>
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">straighten</span>
                    <div class="google-content">
                        <label class="google-label">Unit</label>
                        <input type="text" name="unit" class="google-input" value="pcs" placeholder="e.g. pcs, kg, box">
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">location_on</span>
                    <div class="google-content">
                        <label class="google-label">Location</label>
                        <input type="text" name="location" class="google-input" placeholder="Office, Warehouse A..." value="Shelf A" required>
                    </div>
                </div>
            </div>

            <div class="google-card">
                <div class="google-row" style="align-items: flex-start;">
                    <span class="material-symbols-outlined google-icon" style="margin-top: 2px;">image</span>
                    <div class="google-content">
                        <label class="google-label">Item Image (Optional)</label>
                        <input type="file" name="image" class="google-input" accept="image/*" onchange="previewImage(this)" style="padding-top: 8px; padding-bottom: 8px;">
                        <div class="mt-2">
                            <img id="imagePreview" src="#" alt="Preview" style="max-width: 200px; max-height: 200px; display: none; border-radius: 12px;">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mb-5">
                <button type="submit" class="btn-save">Save Item</button>
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
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        </script>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
