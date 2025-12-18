<?php
$title = "Items & Machines";
ob_start();
?>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                <h1 class="h2">Items & Machines</h1>
                <div class="d-flex gap-3 align-items-center">
                    <form action="" method="GET" class="d-flex">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <span class="material-symbols-outlined text-muted" style="font-size: 20px;">search</span>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search items..." value="<?= htmlspecialchars($search ?? '') ?>">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </form>

                    <?php if ($_SESSION['role'] === 'admin'): ?>
                    <div class="btn-toolbar mb-2 mb-md-0 gap-2">
                        <a href="<?= BASE_URL ?>/items/create-bundle" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined" style="font-size: 18px;">inventory_2</span> New Bundle
                        </a>
                        <a href="<?= BASE_URL ?>/items/create" class="btn btn-sm btn-primary d-flex align-items-center gap-2">
                            <span class="fs-5">+</span> New Item
                        </a>
                    </div>
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
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">Previous</a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                            </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
