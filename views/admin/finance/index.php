<?php
$title = "Finance & Coffers";
ob_start();
?>
<style>
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

    .google-btn-success {
        background-color: #198754;
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
        transition: background-color 0.2s;
    }

    .google-btn-success:hover {
        background-color: #157347;
        color: #fff;
    }

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

    .google-alert {
        display: flex;
        align-items: center;
        background-color: #e6f4ea;
        color: #137333;
        padding: 12px 16px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 14px;
        border: 1px solid #ceead6;
        margin-bottom: 24px;
    }

    .google-alert-danger {
        background-color: #fce8e6;
        color: #c5221f;
        border-color: #fad2cf;
    }
</style>

<div class="row justify-content-center">
    <div class="col-12 col-xxl-11">
        <div class="d-flex justify-content-between flex-wrap align-items-center pt-4 pb-3 mb-3 gap-2">
            <h1 class="h3 mb-0" style="color: #1f1f1f; font-weight: 400;">Finance & Coffers Management</h1>
            <div class="d-flex flex-wrap gap-3 page-header-actions">
                <button type="button" class="google-btn-success shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#depositModal">
                    <span class="material-symbols-outlined" style="font-size: 20px;">add_circle</span> Record Deposit
                </button>
                <button type="button" class="google-add-btn shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#withdrawModal">
                    <span class="material-symbols-outlined" style="font-size: 20px;">payments</span> Record Withdrawal
                </button>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="google-alert shadow-sm">
                <span class="material-symbols-outlined text-success me-2">check_circle</span>
                <span><?= htmlspecialchars($_GET['success']) ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="google-alert google-alert-danger shadow-sm">
                <span class="material-symbols-outlined text-danger me-2">error</span>
                <span><?= htmlspecialchars($_GET['error']) ?></span>
            </div>
        <?php endif; ?>

        <div class="row g-4 mb-5">
            <!-- Revenue Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 24px;">
                    <div class="card-body p-4 text-center">
                        <div class="mb-2">
                            <span class="material-symbols-outlined text-primary"
                                style="font-size: 32px; background: #e8f0fe; padding: 12px; border-radius: 50%;">account_balance_wallet</span>
                        </div>
                        <h6 class="text-muted text-uppercase small mb-2"
                            style="font-weight: 600; letter-spacing: 0.5px;">Total System Revenue</h6>
                        <div class="h3 mb-0 text-primary fw-bold">₵<?= format_large_number($totalRevenue) ?></div>
                        <div class="text-muted small mt-2">All-time sales generated (includes debt/unpaid)</div>
                    </div>
                </div>
            </div>

            <!-- Cash Collected Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 24px;">
                    <div class="card-body p-4 text-center">
                        <div class="mb-2">
                            <span class="material-symbols-outlined text-info"
                                style="font-size: 32px; background: #e0f7fa; padding: 12px; border-radius: 50%;">savings</span>
                        </div>
                        <h6 class="text-muted text-uppercase small mb-2"
                            style="font-weight: 600; letter-spacing: 0.5px;">Cash Collected</h6>
                        <div class="h3 mb-0 text-info fw-bold">₵<?= format_large_number($cashCollected) ?></div>
                        <div class="text-muted small mt-2">All-time realized cash</div>
                    </div>
                </div>
            </div>

            <!-- Profit Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 24px;">
                    <div class="card-body p-4 text-center">
                        <div class="mb-2">
                            <span class="material-symbols-outlined text-success"
                                style="font-size: 32px; background: #e6f4ea; padding: 12px; border-radius: 50%;">trending_up</span>
                        </div>
                        <h6 class="text-muted text-uppercase small mb-2"
                            style="font-weight: 600; letter-spacing: 0.5px;">Total Estimated Profit</h6>
                        <div class="h3 mb-0 text-success fw-bold">₵<?= format_large_number($totalRealizedProfit) ?>
                        </div>
                        <div class="text-muted small mt-2">All-time profit on paid sales</div>
                    </div>
                </div>
            </div>

            <!-- Coffers Balance Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 24px;">
                    <div class="card-body p-4 text-center">
                        <div class="mb-2">
                            <span class="material-symbols-outlined text-warning"
                                style="font-size: 32px; background: #fef7e0; padding: 12px; border-radius: 50%;">account_balance</span>
                        </div>
                        <h6 class="text-muted text-uppercase small mb-2"
                            style="font-weight: 600; letter-spacing: 0.5px;">System Coffers Balance</h6>
                        <div class="h3 mb-0 text-warning fw-bold">₵<?= format_large_number($cofferBalance) ?></div>
                        <div class="text-muted small mt-2">Available liquid cash (Cash Collected - Expenses -
                            Withdrawals + Deposits)</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="google-table-card">
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th class="text-end">Amount</th>
                            <th>Purpose / Details</th>
                            <th>Recorded By</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        foreach ($transactions as $tx):
                            $is_goods = false;
                            $goods_amount = 0;
                            $transport_amount = 0;
                            $display_purpose = $tx['purpose'];

                            if ($tx['type'] === 'withdrawal') {
                                $parsed = @json_decode($tx['purpose'], true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($parsed) && isset($parsed['is_goods'])) {
                                    $is_goods = true;
                                    $goods_amount = $parsed['goods_amount'];
                                    $transport_amount = $parsed['transport_amount'];
                                    $display_purpose = $parsed['description'];
                                }
                            }
                            ?>
                            <tr>
                                <td class="text-muted fw-bold"><?= $i++ ?></td>
                                <td class="fw-bold text-dark" style="font-size: 13px;">
                                    <?= date('M d, Y H:i', strtotime($tx['created_at'])) ?>
                                </td>
                                <td>
                                    <?php if ($tx['type'] === 'deposit'): ?>
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success border rounded-pill px-3 py-1 fw-medium">Deposit</span>
                                    <?php elseif ($is_goods): ?>
                                        <span
                                            class="badge bg-warning bg-opacity-10 text-warning border rounded-pill px-3 py-1 fw-medium">Goods
                                            Withdrawal</span>
                                    <?php else: ?>
                                        <span
                                            class="badge bg-danger bg-opacity-10 text-danger border rounded-pill px-3 py-1 fw-medium">Withdrawal</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end fw-bold <?= $tx['type'] === 'deposit' ? 'text-success' : 'text-danger' ?>"
                                    style="font-size: 15px;">
                                    <?= $tx['type'] === 'deposit' ? '+' : '-' ?>₵<?= number_format($tx['amount'], 2) ?>
                                </td>
                                <td class="text-muted text-uppercase" style="font-size: 13px;">
                                    <?php if ($is_goods): ?>
                                        <div class="d-flex flex-column gap-1">
                                            <span><?= e($display_purpose) ?></span>
                                            <div class="d-flex gap-2">
                                                <span class="badge bg-light text-dark border">Goods:
                                                    ₵<?= number_format($goods_amount, 2) ?></span>
                                                <span class="badge bg-light text-dark border">Transport:
                                                    ₵<?= number_format($transport_amount, 2) ?></span>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <?= e($display_purpose) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1 text-muted">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">person</span>
                                        <small><?= e($tx['recorder_name']) ?></small>
                                    </div>
                                </td>
                                <td class="text-end text-nowrap">
                                    <div class="d-flex justify-content-end gap-1">
                                        <?php if (!$is_goods): ?>
                                            <button type="button" class="action-btn text-secondary edit-tx-btn"
                                                data-id="<?= $tx['id'] ?>" data-amount="<?= $tx['amount'] ?>"
                                                data-purpose="<?= e($tx['purpose']) ?>" title="Edit">
                                                <span class="material-symbols-outlined" style="font-size: 20px;">edit</span>
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="action-btn text-danger delete-tx-btn"
                                            data-id="<?= $tx['id'] ?>" title="Delete">
                                            <span class="material-symbols-outlined" style="font-size: 20px;">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No coffer transactions recorded.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Material Design Modal Styles -->
        <style>
            .material-modal .modal-content {
                border-radius: 28px;
                border: none;
                box-shadow: 0 4px 24px rgba(0, 0, 0, 0.15);
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

            .material-modal .btn-ok:hover {
                background-color: #0842a0;
                color: #fff;
            }

            .google-card {
                background: #fff;
                border-radius: 24px;
                overflow: hidden;
                border: 1px solid #dadce0;
                margin-bottom: 16px;
            }

            .google-row {
                display: flex;
                align-items: center;
                padding: 16px 20px;
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
                margin-right: 16px;
                font-size: 22px;
            }

            .google-content {
                flex-grow: 1;
                display: flex;
                flex-direction: column;
            }

            .google-label {
                font-size: 14px;
                color: #1f1f1f;
                margin-bottom: 2px;
                font-weight: 500;
            }

            .google-input,
            .google-textarea {
                border: none;
                outline: none;
                background: transparent;
                font-size: 15px;
                color: #5f6368;
                padding: 0;
                width: 100%;
            }

            .google-input:focus,
            .google-textarea:focus {
                color: #202124;
            }

            .google-textarea {
                resize: none;
                margin-top: 2px;
            }
        </style>

        <!-- Record Deposit Modal -->
        <div class="modal fade material-modal" id="depositModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
                <div class="modal-content">
                    <form action="<?= BASE_URL ?>/admin/finance/deposit" method="POST" class="prevent-double-submit">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <div class="modal-body p-4">
                            <h4 class="modal-title-custom mb-3">Record Coffer Deposit</h4>
                            <p class="text-muted mb-4" style="font-size: 15px;">Use this to record external funds being
                                added to the business coffers.</p>

                            <div class="google-card">
                                <div class="google-row">
                                    <span class="material-symbols-outlined google-icon">payments</span>
                                    <div class="google-content">
                                        <label class="google-label">Amount (₵)</label>
                                        <input type="number" name="amount"
                                            class="google-input fw-bold text-success fs-5" step="0.01" min="0" required
                                            placeholder="0.00">
                                    </div>
                                </div>
                                <div class="google-row" style="align-items: flex-start;">
                                    <span class="material-symbols-outlined google-icon"
                                        style="margin-top: 2px;">description</span>
                                    <div class="google-content">
                                        <label class="google-label">Source / Purpose</label>
                                        <textarea name="purpose" class="google-textarea" rows="2" required
                                            placeholder="Source of funds or purpose..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end align-items-center gap-2 mt-4">
                                <button type="button" class="btn btn-link btn-cancel"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-ok d-flex align-items-center gap-2"
                                    style="background-color: #198754;">
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
                                    <span class="btn-text">Confirm Deposit</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Record Withdrawal Modal -->
        <div class="modal fade material-modal" id="withdrawModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
                <div class="modal-content">
                    <form action="<?= BASE_URL ?>/admin/finance/withdraw" method="POST" class="prevent-double-submit">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <div class="modal-body p-4">
                            <h4 class="modal-title-custom mb-3">Record Coffer Withdrawal</h4>
                            <p class="text-muted mb-4" style="font-size: 15px;">Use this to record large fund movements
                                from the business coffers.</p>

                            <div class="google-card">
                                <div class="google-row border-bottom">
                                    <span class="material-symbols-outlined google-icon">category</span>
                                    <div class="google-content">
                                        <label class="google-label mb-2">Withdrawal Type</label>
                                        <div class="d-flex gap-4">
                                            <div class="form-check custom-radio">
                                                <input class="form-check-input" type="radio" name="withdrawal_type"
                                                    id="wt_normal" value="normal" checked
                                                    onchange="toggleWithdrawalType()">
                                                <label class="form-check-label text-dark" for="wt_normal">Normal</label>
                                            </div>
                                            <div class="form-check custom-radio">
                                                <input class="form-check-input" type="radio" name="withdrawal_type"
                                                    id="wt_goods" value="goods" onchange="toggleWithdrawalType()">
                                                <label class="form-check-label text-dark" for="wt_goods">For
                                                    Goods</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="normal_amount_div" class="google-row border-bottom">
                                    <span class="material-symbols-outlined google-icon">payments</span>
                                    <div class="google-content">
                                        <label class="google-label">Amount (₵)</label>
                                        <input type="number" name="amount" id="normal_amount"
                                            class="google-input fw-bold text-danger fs-5" step="0.01" min="0" required
                                            placeholder="0.00">
                                    </div>
                                </div>
                                <div id="goods_amount_div" class="google-row border-bottom d-none"
                                    style="align-items: flex-start; gap: 16px;">
                                    <span class="material-symbols-outlined google-icon"
                                        style="margin-top: 8px;">inventory_2</span>
                                    <div class="d-flex flex-column gap-3 flex-grow-1">
                                        <div class="google-content">
                                            <label class="google-label">Goods Amount (₵)</label>
                                            <input type="number" name="goods_amount" id="goods_amount"
                                                class="google-input fw-bold text-danger" step="0.01" min="0"
                                                placeholder="0.00">
                                        </div>
                                        <div class="google-content">
                                            <label class="google-label">Transport Amount (₵)</label>
                                            <input type="number" name="transport_amount" id="transport_amount"
                                                class="google-input fw-bold text-danger" step="0.01" min="0"
                                                placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                                <div class="google-row" style="align-items: flex-start;">
                                    <span class="material-symbols-outlined google-icon"
                                        style="margin-top: 2px;">description</span>
                                    <div class="google-content">
                                        <label class="google-label">Purpose / Description</label>
                                        <textarea name="purpose" class="google-textarea" rows="2" required
                                            placeholder="Description of withdrawal..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end align-items-center gap-2 mt-4">
                                <button type="button" class="btn btn-link btn-cancel"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-ok d-flex align-items-center gap-2">
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
                                    <span class="btn-text">Confirm Withdrawal</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Withdrawal Modal -->
        <div class="modal fade material-modal" id="editTxModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
                <div class="modal-content">
                    <form action="<?= BASE_URL ?>/admin/finance/update" method="POST" class="prevent-double-submit">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="id" id="edit_tx_id">
                        <div class="modal-body p-4">
                            <h4 class="modal-title-custom mb-4">Edit Transaction</h4>

                            <div class="google-card">
                                <div class="google-row">
                                    <span class="material-symbols-outlined google-icon">payments</span>
                                    <div class="google-content">
                                        <label class="google-label">Amount (₵)</label>
                                        <input type="number" name="amount" id="edit_tx_amount"
                                            class="google-input fw-bold fs-5" step="0.01" min="0" required>
                                    </div>
                                </div>
                                <div class="google-row" style="align-items: flex-start;">
                                    <span class="material-symbols-outlined google-icon"
                                        style="margin-top: 2px;">description</span>
                                    <div class="google-content">
                                        <label class="google-label">Purpose / Description</label>
                                        <textarea name="purpose" id="edit_tx_purpose" class="google-textarea" rows="2"
                                            required></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end align-items-center gap-2 mt-4">
                                <button type="button" class="btn btn-link btn-cancel"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-ok d-flex align-items-center gap-2"
                                    style="background-color: #198754;">
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
                                    <span class="btn-text">Save Changes</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Transaction Modal -->
        <div class="modal fade material-modal" id="deleteTxModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
                <div class="modal-content">
                    <div class="modal-body p-4">
                        <h4 class="modal-title-custom text-danger d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined">warning</span> Delete Transaction?
                        </h4>
                        <p class="modal-text text-muted mb-4">The money will be returned to the coffers. This action
                            cannot be undone.</p>
                        <div class="d-flex justify-content-end align-items-center gap-2">
                            <button type="button" class="btn btn-link btn-cancel"
                                data-bs-dismiss="modal">Cancel</button>
                            <form id="deleteForm" action="<?= BASE_URL ?>/admin/finance/delete" method="POST"
                                class="m-0">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="id" id="delete_tx_id">
                                <button type="submit" class="btn btn-ok bg-danger text-white">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Modal data population
        const editModal = new bootstrap.Modal(document.getElementById('editTxModal'));
        document.querySelectorAll('.edit-tx-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.getElementById('edit_tx_id').value = this.dataset.id;
                document.getElementById('edit_tx_amount').value = this.dataset.amount;
                document.getElementById('edit_tx_purpose').value = this.dataset.purpose;
                editModal.show();
            });
        });

        // Delete Modal
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteTxModal'));
        document.querySelectorAll('.delete-tx-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.getElementById('delete_tx_id').value = this.dataset.id;
                deleteModal.show();
            });
        });

        // Double submission prevention
        document.querySelectorAll('.prevent-double-submit').forEach(form => {
            form.addEventListener('submit', function () {
                const btn = this.querySelector('button[type="submit"]');
                const spinner = btn.querySelector('.spinner-border');
                const btnText = btn.querySelector('.btn-text');

                btn.disabled = true;
                if (spinner) spinner.classList.remove('d-none');
                if (btnText) btnText.innerText = 'Processing...';
            });
        });
    });

    function toggleWithdrawalType() {
        const isGoods = document.getElementById('wt_goods').checked;
        const normalDiv = document.getElementById('normal_amount_div');
        const goodsDiv = document.getElementById('goods_amount_div');
        const normalInput = document.getElementById('normal_amount');
        const goodsInput = document.getElementById('goods_amount');
        const transportInput = document.getElementById('transport_amount');

        if (isGoods) {
            normalDiv.classList.add('d-none');
            normalInput.removeAttribute('required');

            goodsDiv.classList.remove('d-none');
            goodsInput.setAttribute('required', 'required');
            transportInput.setAttribute('required', 'required');
        } else {
            goodsDiv.classList.add('d-none');
            goodsInput.removeAttribute('required');
            transportInput.removeAttribute('required');

            normalDiv.classList.remove('d-none');
            normalInput.setAttribute('required', 'required');
        }
    }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>