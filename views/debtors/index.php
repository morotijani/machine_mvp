<?php
$title = "Debt System";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-12 col-xxl-11">
        <style>
            .google-search-wrapper {
                height: 44px;
                border-radius: 22px;
                border: 1px solid #dadce0;
                background-color: #fff;
                display: flex;
                overflow: hidden;
                flex-grow: 1;
                min-width: 280px;
            }
            .google-search-icon {
                display: flex;
                align-items: center;
                padding-left: 16px;
                color: #5f6368;
            }
            .google-search-input {
                border: none !important;
                outline: none !important;
                box-shadow: none !important;
                background: transparent;
                padding-left: 8px;
                font-size: 15px;
                color: #202124;
                flex-grow: 1;
            }
            .google-search-input::placeholder {
                color: #5f6368;
            }
            .google-search-btn {
                background-color: #3b71ca;
                color: #fff;
                border: none;
                font-weight: 500;
                padding: 0 24px;
                font-size: 15px;
                transition: background-color 0.2s;
            }
            .google-search-btn:hover {
                background-color: #2b5db0;
            }
            .google-add-btn {
                background-color: #3b71ca;
                color: #fff;
                border: none;
                border-radius: 22px;
                height: 44px;
                padding: 0 24px;
                font-weight: 500;
                font-size: 15px;
                display: flex;
                align-items: center;
                gap: 8px;
                text-decoration: none;
                transition: background-color 0.2s;
            }
            .google-add-btn:hover {
                background-color: #2b5db0;
                color: #fff;
            }
        </style>

        <div class="d-flex justify-content-between flex-wrap align-items-center pt-4 pb-3 mb-3 gap-2">
            <h1 class="h3 mb-0" style="color: #1f1f1f; font-weight: 400;">Standalone Debt System</h1>
            <div class="d-flex flex-wrap gap-3 align-items-center justify-content-start justify-content-md-end flex-grow-1">
                <form action="<?= BASE_URL ?>/debtors" method="GET" class="d-flex flex-grow-1 flex-md-grow-0 gap-2">
                    <div class="google-search-wrapper">
                        <div class="google-search-icon">
                            <span class="material-symbols-outlined" style="font-size: 20px;">search</span>
                        </div>
                        <input type="text" name="search" class="form-control google-search-input" placeholder="Search debtor name or phone..." value="<?= e($_GET['search'] ?? '') ?>">
                        <button type="submit" class="google-search-btn">Search</button>
                    </div>
                    
                    <?php if (!empty($_GET['search'])): ?>
                        <a href="<?= BASE_URL ?>/debtors" class="btn btn-light rounded-pill px-3 d-flex align-items-center" style="border: 1px solid #dadce0;">Clear</a>
                    <?php endif; ?>
                </form>

                <div class="d-flex flex-wrap page-header-actions">
                    <a href="<?= BASE_URL ?>/debtors/create" class="google-add-btn">
                        <span class="material-symbols-outlined" style="font-size: 20px;">person_add</span> Add New Debtor
                    </a>
                </div>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= e($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <style>
            .google-table-card {
                background: #fff;
                border-radius: 24px;
                overflow: hidden;
                border: none;
                box-shadow: none;
                margin-bottom: 30px;
            }
            .google-table-card thead th {
                border-bottom: 1px solid #e3e3e3;
                background-color: #fff;
                color: #5f6368;
                font-weight: 500;
                padding: 16px 24px;
                font-size: 14px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .google-table-card tbody td {
                padding: 16px 24px;
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
            .action-btn {
                border: none;
                background: transparent;
                color: #5f6368;
                border-radius: 50%;
                width: 36px;
                height: 36px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: background-color 0.2s, color 0.2s;
                text-decoration: none !important;
            }
            .action-btn:hover {
                background-color: #f1f3f4;
                color: #202124;
            }
            .google-badge {
                padding: 4px 10px;
                border-radius: 12px;
                font-size: 12px;
                letter-spacing: 0.5px;
                font-weight: 600;
                display: inline-block;
            }
            .badge-subtle-success {
                background-color: #e6f4ea;
                color: #137333;
                border: 1px solid #ceead6;
            }
            .badge-subtle-warning {
                background-color: #fef7e0;
                color: #b06000;
                border: 1px solid #feefc3;
            }
            .badge-subtle-danger {
                background-color: #fce8e6;
                color: #c5221f;
                border: 1px solid #fad2cf;
            }
            
            /* Modal styling */
            .material-modal .modal-content {
                border-radius: 28px;
                border: none;
                box-shadow: 0 4px 24px rgba(0,0,0,0.15);
            }
            .material-modal .modal-title-custom {
                font-size: 24px;
                color: #1f1f1f;
                font-weight: 400;
                margin-bottom: 16px;
            }
            .material-modal .btn-cancel {
                color: #0b57d0;
                font-weight: 500;
                text-decoration: none;
                padding: 10px 16px;
                border-radius: 20px;
            }
            .material-modal .btn-cancel:hover {
                background-color: #f6f8fb;
            }
            .material-modal .btn-ok {
                background-color: #0b57d0;
                color: #fff;
                font-weight: 500;
                border-radius: 20px;
                padding: 10px 24px;
                border: none;
                transition: background-color 0.2s;
            }
        </style>

        <div class="google-table-card">
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <thead>
                        <tr>
                                <th style="width: 50px;">#</th>
                                <th>Debtor Name</th>
                                <th>Contact</th>
                                <th class="text-end">Total Debt</th>
                                <th class="text-end">Paid</th>
                                <th class="text-end">Balance</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($debtors as $d): ?>
                            <?php $balance = $d['total_amount'] - $d['paid_amount']; ?>
                            <tr>
                                <td class="text-muted fw-bold"><?= $i++ ?></td>
                                <td>
                                    <strong><?= e($d['name']) ?></strong>
                                    <br><small class="text-muted"><?= e($d['description']) ?></small>
                                </td>
                                <td><?= e($d['phone'] ?: 'N/A') ?></td>
                                <td class="text-end">₵<?= number_format($d['total_amount'], 2) ?></td>
                                <td class="text-end text-success">₵<?= number_format($d['paid_amount'], 2) ?></td>
                                <td class="text-end fw-bold text-danger">₵<?= number_format($balance, 2) ?></td>
                                <td>
                                    <?php if ($d['status'] === 'cleared'): ?>
                                        <span class="google-badge badge-subtle-success">Cleared</span>
                                    <?php elseif ($d['status'] === 'partially_paid'): ?>
                                        <span class="google-badge badge-subtle-warning">Partial</span>
                                    <?php else: ?>
                                        <span class="google-badge badge-subtle-danger">Unpaid</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end text-nowrap">
                                        <?php if ($balance > 0): ?>
                                            <a href="<?= BASE_URL ?>/debtors/payment?id=<?= $d['id'] ?>" class="action-btn text-success" title="Record Payment">
                                                <span class="material-symbols-outlined" style="font-size: 20px;">payments</span>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($_SESSION['role'] === 'admin'): ?>
                                            <a href="<?= BASE_URL ?>/debtors/increase?id=<?= $d['id'] ?>" class="action-btn text-warning" title="Increase Debt">
                                                <span class="material-symbols-outlined" style="font-size: 20px;">add_circle</span>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= BASE_URL ?>/debtors/history?id=<?= $d['id'] ?>" class="action-btn text-info" title="Payment History">
                                            <span class="material-symbols-outlined" style="font-size: 20px;">history</span>
                                        </a>
                                        <?php if ($_SESSION['role'] === 'admin'): ?>
                                        <form id="deleteForm<?= $d['id'] ?>" action="<?= BASE_URL ?>/debtors/delete" method="POST" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                            <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                            <button type="button" class="action-btn text-danger" onclick="confirmDelete(<?= $d['id'] ?>)" title="Soft Delete">
                                                <span class="material-symbols-outlined" style="font-size: 20px;">delete</span>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($debtors)): ?>
                                <tr><td colspan="8" class="text-center py-4 text-muted">No debtors found.</td></tr>
                            <?php endif; ?>
                    </tbody>
                </table>
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
                    <span class="material-symbols-outlined">warning</span> Delete Debtor?
                </h4>
                <p class="modal-text text-muted mb-4">Soft-delete this customer? They can be restored from Trash.</p>
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <button type="button" class="btn btn-link btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" action="<?= BASE_URL ?>/debtors/delete" method="POST" class="m-0">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="id" id="delete_id">
                        <button type="submit" class="btn btn-ok bg-danger text-white">Soft Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    document.getElementById('delete_id').value = id;
    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    modal.show();
}
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
