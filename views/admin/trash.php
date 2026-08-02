<?php
$title = "Recycle Bin";
ob_start();
?>
<style>
    .google-btn {
        border-radius: 24px;
        padding: 8px 24px;
        font-weight: 500;
        border: none;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        font-size: 14px;
        cursor: pointer;
    }
    .google-btn-icon {
        border-radius: 50%;
        width: 36px;
        height: 36px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: transparent;
        transition: all 0.2s;
        color: #5f6368;
    }
    .google-btn-icon:hover {
        background-color: #f1f3f4;
        color: #1f1f1f;
    }
    .google-btn-icon.text-danger:hover {
        background-color: #fce8e6;
        color: #d93025 !important;
    }
    .google-btn-icon.text-success:hover {
        background-color: #e6f4ea;
        color: #137333 !important;
    }
    .google-table-card {
        background: #fff;
        border-radius: 24px;
        overflow: hidden;
        border: none;
        box-shadow: none;
        margin-bottom: 24px;
    }
    .google-table-card .card-header {
        background-color: #fff;
        border-bottom: 1px solid #e3e3e3;
        padding: 24px 32px;
    }
    .google-table-card .card-body {
        padding: 0;
    }
    .google-table-card table {
        margin-bottom: 0;
        width: 100%;
    }
    .google-table-card thead th {
        border-bottom: 1px solid #e3e3e3;
        background-color: #fff;
        color: #5f6368;
        font-weight: 500;
        padding: 16px 32px;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .google-table-card tbody td {
        padding: 16px 32px;
        border-bottom: 1px solid #e3e3e3;
        color: #1f1f1f;
        vertical-align: middle;
    }
    .google-table-card tbody tr:last-child td {
        border-bottom: none;
    }
    .google-table-card tbody tr {
        transition: background-color 0.2s;
    }
    .google-table-card tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .google-nav-pills {
        gap: 8px;
    }
    .google-nav-pills .nav-link {
        color: #444746;
        border-radius: 24px;
        font-weight: 500;
        padding: 8px 20px;
        font-size: 14px;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    .google-nav-pills .nav-link:hover:not(.active) {
        background-color: #f1f3f4;
    }
    .google-nav-pills .nav-link.active {
        background-color: #e8f0fe;
        color: #0b57d0;
    }

    .google-alert {
        border-radius: 16px;
        border: none;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        font-weight: 500;
        font-size: 14px;
    }
    .google-alert-success {
        background-color: #e6f4ea;
        color: #137333;
    }
    .google-alert-danger {
        background-color: #fce8e6;
        color: #d93025;
    }
    .google-alert .btn-close {
        padding: 16px;
    }

    /* Modal Styling matching expenditures */
    .material-modal .modal-content {
        border: none;
        border-radius: 24px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.15);
    }
    .material-modal .modal-title-custom {
        font-size: 20px;
        font-weight: 500;
        margin-bottom: 16px;
    }
    .material-modal .modal-text {
        font-size: 15px;
        line-height: 1.5;
    }
    .material-modal .btn-cancel {
        color: #0b57d0;
        text-decoration: none;
        font-weight: 500;
        padding: 8px 16px;
        border-radius: 24px;
    }
    .material-modal .btn-cancel:hover {
        background-color: #f6f8fb;
    }
    .material-modal .btn-ok {
        border-radius: 24px;
        padding: 8px 24px;
        font-weight: 500;
        border: none;
    }
</style>

<div class="row justify-content-center">
    <div class="col-12 col-xxl-11">
        <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-4 gap-2">
            <h1 class="h3 mb-0" style="color: #1f1f1f; font-weight: 400;">Recycle Bin</h1>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert google-alert google-alert-success alert-dismissible fade show" role="alert">
                <span class="material-symbols-outlined" style="font-size: 20px;">check_circle</span>
                <div><?= htmlspecialchars($_GET['success']) ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert google-alert google-alert-danger alert-dismissible fade show" role="alert">
                <span class="material-symbols-outlined" style="font-size: 20px;">error</span>
                <div><?= htmlspecialchars($_GET['error']) ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="google-table-card">
            <div class="card-header border-0 pt-4 pb-3">
                <ul class="nav nav-pills google-nav-pills px-2" id="trashTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active d-flex align-items-center gap-2" id="items-tab" data-bs-toggle="tab" data-bs-target="#items" type="button" role="tab">
                            <span class="material-symbols-outlined" style="font-size: 18px;">inventory_2</span> Items
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                            <span class="material-symbols-outlined" style="font-size: 18px;">group</span> Users
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button" role="tab">
                            <span class="material-symbols-outlined" style="font-size: 18px;">receipt_long</span> Sales
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2" id="expenditure-tab" data-bs-toggle="tab" data-bs-target="#expenditures" type="button" role="tab">
                            <span class="material-symbols-outlined" style="font-size: 18px;">payments</span> Expenditures
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2" id="customers-tab" data-bs-toggle="tab" data-bs-target="#customers" type="button" role="tab">
                            <span class="material-symbols-outlined" style="font-size: 18px;">person_outline</span> Customers
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="trashTabsContent">
                    <!-- Items Tab -->
                    <div class="tab-pane fade show active" id="items" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
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
                                        <td>
                                            <div style="font-weight: 500; color: #1f1f1f;"><?= htmlspecialchars($item['name']) ?></div>
                                        </td>
                                        <td><span class="text-muted" style="font-size: 13px;"><?= htmlspecialchars($item['sku']) ?></span></td>
                                        <td><?= htmlspecialchars($item['category']) ?></td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                <form action="<?= BASE_URL ?>/admin/restore" method="POST" class="m-0">
                                                    <input type="hidden" name="type" value="item">
                                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                    <button type="submit" class="google-btn-icon text-success" title="Restore">
                                                        <span class="material-symbols-outlined" style="font-size: 20px;">settings_backup_restore</span>
                                                    </button>
                                                </form>
                                                <button type="button" class="google-btn-icon text-danger" title="Delete Forever" onclick="confirmDeleteForever('item', <?= $item['id'] ?>, 'This cannot be undone. Are you sure you want to permanently delete this item?')">
                                                    <span class="material-symbols-outlined" style="font-size: 20px;">delete_forever</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($deletedItems)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <span class="material-symbols-outlined d-block mb-2" style="font-size: 48px; color: #c7d0dd;">inventory_2</span>
                                                No deleted items.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Users Tab -->
                    <div class="tab-pane fade" id="users" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Name / Details</th>
                                        <th>Role</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($deletedUsers as $user): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div style="font-weight: 500; color: #1f1f1f;"><?= htmlspecialchars($user['fullname'] ?: 'N/A') ?></div>
                                                <span style="font-size: 10px; background: #f1f3f4; color: #444746; padding: 2px 8px; border-radius: 12px; letter-spacing: 0.5px;">@<?= htmlspecialchars($user['username']) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="border-radius: 16px; padding: 4px 12px; font-size: 12px; font-weight: 500; display: inline-flex; <?= $user['role'] === 'admin' ? 'background-color: #f1f3f4; color: #1f1f1f;' : 'background-color: #e8f0fe; color: #0b57d0;' ?> text-transform: uppercase;">
                                                <?= ucfirst($user['role']) ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                <form action="<?= BASE_URL ?>/admin/restore" method="POST" class="m-0">
                                                    <input type="hidden" name="type" value="user">
                                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                                    <button type="submit" class="google-btn-icon text-success" title="Restore">
                                                        <span class="material-symbols-outlined" style="font-size: 20px;">settings_backup_restore</span>
                                                    </button>
                                                </form>
                                                <button type="button" class="google-btn-icon text-danger" title="Delete Forever" onclick="confirmDeleteForever('user', <?= $user['id'] ?>, 'This cannot be undone. Are you sure you want to permanently delete this user?')">
                                                    <span class="material-symbols-outlined" style="font-size: 20px;">delete_forever</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($deletedUsers)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center py-5 text-muted">
                                                <span class="material-symbols-outlined d-block mb-2" style="font-size: 48px; color: #c7d0dd;">group</span>
                                                No deleted users.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Sales Tab -->
                    <div class="tab-pane fade" id="sales" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
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
                                        <td><div style="font-weight: 500; color: #0b57d0;">#<?= htmlspecialchars($sale['id']) ?></div></td>
                                        <td><?= htmlspecialchars($sale['customer_name']) ?></td>
                                        <td class="text-end fw-medium" style="color: #1f1f1f;">₵<?= format_large_number($sale['total_amount']) ?></td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                <form action="<?= BASE_URL ?>/admin/restore" method="POST" class="m-0">
                                                    <input type="hidden" name="type" value="sale">
                                                    <input type="hidden" name="id" value="<?= $sale['id'] ?>">
                                                    <button type="submit" class="google-btn-icon text-success" title="Restore">
                                                        <span class="material-symbols-outlined" style="font-size: 20px;">settings_backup_restore</span>
                                                    </button>
                                                </form>
                                                <button type="button" class="google-btn-icon text-danger" title="Delete Forever" onclick="confirmDeleteForever('sale', <?= $sale['id'] ?>, 'This will also permanently delete all associated payments and stock history. Proceed?')">
                                                    <span class="material-symbols-outlined" style="font-size: 20px;">delete_forever</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($deletedSales)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <span class="material-symbols-outlined d-block mb-2" style="font-size: 48px; color: #c7d0dd;">receipt_long</span>
                                                No voided sales.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Expenditures Tab -->
                    <div class="tab-pane fade" id="expenditures" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
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
                                        <td>
                                            <div style="font-weight: 500; color: #1f1f1f;"><?= date('M d, Y', strtotime($exp['date'])) ?></div>
                                        </td>
                                        <td>
                                            <span style="border-radius: 12px; background: #f1f3f4; color: #444746; padding: 4px 10px; font-size: 12px; font-weight: 500;">
                                                <?= htmlspecialchars($exp['category']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($exp['recorder_name'] ?? 'N/A') ?></td>
                                        <td class="text-end fw-medium" style="color: #d93025;">₵<?= format_large_number($exp['amount']) ?></td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                <form action="<?= BASE_URL ?>/admin/restore" method="POST" class="m-0">
                                                    <input type="hidden" name="type" value="expenditure">
                                                    <input type="hidden" name="id" value="<?= $exp['id'] ?>">
                                                    <button type="submit" class="google-btn-icon text-success" title="Restore">
                                                        <span class="material-symbols-outlined" style="font-size: 20px;">settings_backup_restore</span>
                                                    </button>
                                                </form>
                                                <button type="button" class="google-btn-icon text-danger" title="Delete Forever" onclick="confirmDeleteForever('expenditure', <?= $exp['id'] ?>, 'This cannot be undone. Are you sure you want to permanently delete this expenditure?')">
                                                    <span class="material-symbols-outlined" style="font-size: 20px;">delete_forever</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($deletedExpenditures)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <span class="material-symbols-outlined d-block mb-2" style="font-size: 48px; color: #c7d0dd;">payments</span>
                                                No deleted expenditures.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Customers Tab -->
                    <div class="tab-pane fade" id="customers" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Customer Details</th>
                                        <th>Phone</th>
                                        <th class="text-end">Balance (Debt)</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($deletedCustomers as $customer): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 500; color: #1f1f1f;"><?= htmlspecialchars($customer['name']) ?></div>
                                            <div class="text-muted" style="font-size: 13px;"><?= htmlspecialchars($customer['address'] ?? '') ?></div>
                                        </td>
                                        <td><?= htmlspecialchars($customer['phone'] ?? 'N/A') ?></td>
                                        <td class="text-end fw-medium <?= $customer['total_debt'] > 0 ? 'text-danger' : 'text-success' ?>">
                                            ₵<?= format_large_number($customer['total_debt']) ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                <form action="<?= BASE_URL ?>/admin/restore" method="POST" class="m-0">
                                                    <input type="hidden" name="type" value="customer">
                                                    <input type="hidden" name="id" value="<?= $customer['id'] ?>">
                                                    <button type="submit" class="google-btn-icon text-success" title="Restore">
                                                        <span class="material-symbols-outlined" style="font-size: 20px;">settings_backup_restore</span>
                                                    </button>
                                                </form>
                                                <button type="button" class="google-btn-icon text-danger" title="Delete Forever" onclick="confirmDeleteForever('customer', <?= $customer['id'] ?>, 'This cannot be undone. Customers with transaction history cannot be deleted forever. Proceed?')">
                                                    <span class="material-symbols-outlined" style="font-size: 20px;">delete_forever</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($deletedCustomers)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <span class="material-symbols-outlined d-block mb-2" style="font-size: 48px; color: #c7d0dd;">person_outline</span>
                                                No deleted customers.
                                            </td>
                                        </tr>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade material-modal" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content">
            <div class="modal-body p-4">
                <h4 class="modal-title-custom text-danger d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined">warning</span> Permanent Delete
                </h4>
                <p class="modal-text text-muted mb-4" id="deleteConfirmMessage">This action cannot be undone. Are you sure?</p>
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <button type="button" class="btn btn-link btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForeverForm" action="<?= BASE_URL ?>/admin/delete-forever" method="POST" class="m-0">
                        <input type="hidden" name="type" id="delete_forever_type">
                        <input type="hidden" name="id" id="delete_forever_id">
                        <button type="submit" class="btn btn-ok bg-danger text-white">Delete Forever</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDeleteForever(type, id, message) {
    document.getElementById('delete_forever_type').value = type;
    document.getElementById('delete_forever_id').value = id;
    document.getElementById('deleteConfirmMessage').innerText = message;
    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    modal.show();
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
