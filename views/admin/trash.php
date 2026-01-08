<?php
$title = "Recycle Bin";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
            <h1 class="h2">Recycle Bin</h1>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-4">
                <ul class="nav nav-pills" id="trashTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active d-flex align-items-center gap-2" id="items-tab" data-bs-toggle="tab" data-bs-target="#items" type="button" role="tab">
                            <span class="material-symbols-outlined">inventory_2</span> Items
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                            <span class="material-symbols-outlined">group</span> Users
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button" role="tab">
                            <span class="material-symbols-outlined">receipt_long</span> Sales
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2" id="expenditure-tab" data-bs-toggle="tab" data-bs-target="#expenditures" type="button" role="tab">
                            <span class="material-symbols-outlined">payments</span> Expenditures
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="trashTabsContent">
                    <!-- Items Tab -->
                    <div class="tab-pane fade show active" id="items" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>SKU</th>
                                        <th>Category</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($deletedItems as $item): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($item['name']) ?></strong></td>
                                        <td><span class="text-muted"><?= htmlspecialchars($item['sku']) ?></span></td>
                                        <td><?= htmlspecialchars($item['category']) ?></td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <form action="<?= BASE_URL ?>/admin/restore" method="POST">
                                                    <input type="hidden" name="type" value="item">
                                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Restore">
                                                        <span class="material-symbols-outlined" style="font-size: 18px;">settings_backup_restore</span>
                                                    </button>
                                                </form>
                                                <form action="<?= BASE_URL ?>/admin/delete-forever" method="POST" onsubmit="return confirm('PERMANENT DELETE: This cannot be undone. Are you sure?')">
                                                    <input type="hidden" name="type" value="item">
                                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Forever">
                                                        <span class="material-symbols-outlined" style="font-size: 18px;">delete_forever</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($deletedItems)): ?>
                                        <tr><td colspan="4" class="text-center py-4 text-muted small">No deleted items.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Users Tab -->
                    <div class="tab-pane fade" id="users" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Full Name</th>
                                        <th>Role</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($deletedUsers as $user): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($user['username']) ?></strong></td>
                                        <td><?= htmlspecialchars($user['fullname'] ?: 'N/A') ?></td>
                                        <td><?= ucfirst($user['role']) ?></td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <form action="<?= BASE_URL ?>/admin/restore" method="POST">
                                                    <input type="hidden" name="type" value="user">
                                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                                        <span class="material-symbols-outlined" style="font-size: 18px;">settings_backup_restore</span>
                                                    </button>
                                                </form>
                                                <form action="<?= BASE_URL ?>/admin/delete-forever" method="POST" onsubmit="return confirm('PERMANENT DELETE: This cannot be undone. Are you sure?')">
                                                    <input type="hidden" name="type" value="user">
                                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <span class="material-symbols-outlined" style="font-size: 18px;">delete_forever</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($deletedUsers)): ?>
                                        <tr><td colspan="4" class="text-center py-4 text-muted small">No deleted users.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Sales Tab -->
                    <div class="tab-pane fade" id="sales" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Customer</th>
                                        <th class="text-end">Total Amount</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($deletedSales as $sale): ?>
                                    <tr>
                                        <td><strong>#<?= htmlspecialchars($sale['id']) ?></strong></td>
                                        <td><?= htmlspecialchars($sale['customer_name']) ?></td>
                                        <td class="text-end fw-bold">₵<?= number_format($sale['total_amount'], 2) ?></td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <form action="<?= BASE_URL ?>/admin/restore" method="POST">
                                                    <input type="hidden" name="type" value="sale">
                                                    <input type="hidden" name="id" value="<?= $sale['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                                        <span class="material-symbols-outlined" style="font-size: 18px;">settings_backup_restore</span>
                                                    </button>
                                                </form>
                                                <form action="<?= BASE_URL ?>/admin/delete-forever" method="POST" onsubmit="return confirm('PERMANENT DELETE: This will also delete all associated payments and stock history. Proceed?')">
                                                    <input type="hidden" name="type" value="sale">
                                                    <input type="hidden" name="id" value="<?= $sale['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <span class="material-symbols-outlined" style="font-size: 18px;">delete_forever</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($deletedSales)): ?>
                                        <tr><td colspan="4" class="text-center py-4 text-muted small">No voided sales.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Expenditures Tab -->
                    <div class="tab-pane fade" id="expenditures" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>Recorded By</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($deletedExpenditures as $exp): ?>
                                    <tr>
                                        <td><strong><?= date('M d, Y', strtotime($exp['date'])) ?></strong></td>
                                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary"><?= htmlspecialchars($exp['category']) ?></span></td>
                                        <td><?= htmlspecialchars($exp['recorder_name'] ?? 'N/A') ?></td>
                                        <td class="text-end fw-bold text-danger">₵<?= number_format($exp['amount'], 2) ?></td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <form action="<?= BASE_URL ?>/admin/restore" method="POST">
                                                    <input type="hidden" name="type" value="expenditure">
                                                    <input type="hidden" name="id" value="<?= $exp['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                                        <span class="material-symbols-outlined" style="font-size: 18px;">settings_backup_restore</span>
                                                    </button>
                                                </form>
                                                <form action="<?= BASE_URL ?>/admin/delete-forever" method="POST" onsubmit="return confirm('PERMANENT DELETE: This cannot be undone. Proceed?')">
                                                    <input type="hidden" name="type" value="expenditure">
                                                    <input type="hidden" name="id" value="<?= $exp['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <span class="material-symbols-outlined" style="font-size: 18px;">delete_forever</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($deletedExpenditures)): ?>
                                        <tr><td colspan="5" class="text-center py-4 text-muted small">No deleted expenditures.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-pills .nav-link {
        color: #6c757d;
        border-radius: 8px;
        font-weight: 500;
        padding: 10px 20px;
    }
    .nav-pills .nav-link.active {
        background-color: #f8f9fa;
        color: #0d6efd;
        box-shadow: inset 0 0 0 1px #0d6efd;
    }
    .smaller { font-size: 0.75rem; }
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
