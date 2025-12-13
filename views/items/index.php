<?php
$title = "Items & Machines";
ob_start();
?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                <h1 class="h2">Items & Machines</h1>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="<?= BASE_URL ?>/items/create" class="btn btn-sm btn-primary d-flex align-items-center gap-2">
                        <span class="fs-5">+</span> Add New Item
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                <th style="width: 60px;">Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>SKU</th>
                                <th>Location</th>
                                <th class="text-end">Price</th>
                                <th class="text-center">Stock</th>
                                <?php if ($_SESSION['role'] === 'admin'): ?>
                                <th class="text-end">Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($item['image_path'])): ?>
                                        <img src="<?= BASE_URL ?>/<?php echo $item['image_path']; ?>" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted small" style="width: 40px; height: 40px;">No Img</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($item['name']); ?></div>
                                </td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary"><?php echo htmlspecialchars($item['category']); ?></span></td>
                                <td><small class="text-muted"><?php echo htmlspecialchars($item['sku']); ?></small></td>
                                <td><?php echo htmlspecialchars($item['location']); ?></td>
                                <td class="text-end fw-bold text-primary">₵<?php echo number_format($item['price'], 2); ?></td>
                                <td class="text-center">
                                    <?php if ($item['quantity'] <= 5): ?>
                                        <span class="badge bg-danger"><?php echo $item['quantity']; ?> <?php echo $item['unit']; ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?php echo $item['quantity']; ?> <?php echo $item['unit']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($_SESSION['role'] === 'admin'): ?>
                                <td class="text-end">
                                    <a href="<?= BASE_URL ?>/items/edit?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No items found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
