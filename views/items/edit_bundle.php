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

<form action="<?= BASE_URL ?>/items/edit?id=<?= $item['id'] ?>" method="POST" enctype="multipart/form-data">
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
                            <input type="text" name="sku" class="form-control" required value="<?= htmlspecialchars($item['sku']) ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($item['location']) ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bundle Price (Selling)</label>
                        <div class="input-group">
                            <span class="input-group-text">₵</span>
                            <input type="number" name="price" step="0.01" class="form-control" required value="<?= $item['price'] ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Stock</label>
                        <input type="text" class="form-control" value="<?= $item['quantity'] ?> Bundles" disabled>
                        <div class="form-text text-warning">
                            To change stock quantity, you must Create New Bundles or Ungroup existing ones.
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
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Bundle Components (Read Only)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>SKU</th>
                                    <th>Qty per Bundle</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($components as $comp): ?>
                                <tr>
                                    <td><?= htmlspecialchars($comp['name']) ?></td>
                                    <td><?= htmlspecialchars($comp['sku']) ?></td>
                                    <td><?= $comp['quantity'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3">
                        <div class="alert alert-info mb-0">
                            <span class="material-symbols-outlined align-middle fs-6">info</span>
                            Bundle composition cannot be edited once created to ensure stock accuracy. 
                            To change composition, please create a new bundle definition.
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white text-end">
                     <button type="submit" class="btn btn-primary px-4">Update Bundle Details</button>
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

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
