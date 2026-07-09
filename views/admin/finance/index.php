<?php
$title = "Finance & Coffers";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-12 col-xxl-11">
        <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 gap-2">
            <h1 class="h2 mb-0">Finance & Coffers Management</h1>
            <div class="d-flex flex-wrap gap-2 page-header-actions">
                <button type="button" class="btn btn-sm btn-outline-success rounded-pill d-flex align-items-center gap-1 px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#depositModal">
                    <span class="material-symbols-outlined" style="font-size: 18px;">add_circle</span> Record Deposit
                </button>
                <button type="button" class="btn btn-sm btn-primary rounded-pill d-flex align-items-center gap-1 px-3 fw-medium shadow-sm" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                    <span class="material-symbols-outlined" style="font-size: 18px;">payments</span> Record Withdrawal
                </button>
            </div>
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

<div class="row g-4 mb-4">
    <!-- Revenue Card -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-primary border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-2">Total System Revenue</h6>
                <div class="h3 mb-0 text-primary">₵<?= number_format($totalRevenue, 2) ?></div>
                <div class="text-muted small mt-2">All-time realized cash (Paid Amount)</div>
            </div>
        </div>
    </div>
    
    <!-- Profit Card -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-success border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-2">Total Estimated Profit</h6>
                <div class="h3 mb-0 text-success">₵<?= number_format($totalRealizedProfit, 2) ?></div>
                <div class="text-muted small mt-2">All-time realized profit on paid sales</div>
            </div>
        </div>
    </div>

    <!-- Coffers Balance Card -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-warning border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-2">System Coffers Balance</h6>
                <div class="h3 mb-0 text-warning">₵<?= number_format($cofferBalance, 2) ?></div>
                <div class="text-muted small mt-2">Available liquid cash (Paid - Exp - Wdr + Dep)</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Recent Coffer Transactions</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Purpose / Details</th>
                                <th>Recorded By</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $tx): 
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
                                <td><?= date('M j, Y H:i', strtotime($tx['created_at'])) ?></td>
                                <td>
                                    <?php if ($tx['type'] === 'deposit'): ?>
                                        <span class="badge bg-success-subtle text-success">Deposit</span>
                                    <?php elseif ($is_goods): ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Goods Withdrawal</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger">Withdrawal</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold <?= $tx['type'] === 'deposit' ? 'text-success' : 'text-danger' ?>">
                                    <?= $tx['type'] === 'deposit' ? '+' : '-' ?>₵<?= number_format($tx['amount'], 2) ?>
                                </td>
                                <td>
                                    <?php if ($is_goods): ?>
                                        <div class="d-flex flex-column gap-1">
                                            <span><?= e($display_purpose) ?></span>
                                            <div class="d-flex gap-2">
                                                <span class="badge bg-light text-dark border">Goods: ₵<?= number_format($goods_amount, 2) ?></span>
                                                <span class="badge bg-light text-dark border">Transport: ₵<?= number_format($transport_amount, 2) ?></span>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <?= e($display_purpose) ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($tx['recorder_name']) ?></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <?php if (!$is_goods): ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary edit-tx-btn" 
                                                data-id="<?= $tx['id'] ?>" 
                                                data-amount="<?= $tx['amount'] ?>" 
                                                data-purpose="<?= e($tx['purpose']) ?>">
                                            <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                                        </button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-tx-btn" data-id="<?= $tx['id'] ?>">
                                            <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No coffer transactions recorded.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Material Design Modal Styles -->
<style>
    .material-modal .modal-content { border-radius: 28px; }
    .material-modal .modal-title-custom { font-size: 24px; color: #1f1f1f; font-weight: 400; margin-bottom: 16px; }
    .material-modal .btn-cancel { color: #0b57d0; font-weight: 500; text-decoration: none; padding: 10px 16px; border-radius: 20px; }
    .material-modal .btn-cancel:hover { background-color: #f6f8fb; }
    .material-modal .btn-ok { background-color: #0b57d0; color: #fff; font-weight: 500; border-radius: 20px; padding: 10px 24px; border: none; transition: background-color 0.2s; }
    .material-modal .btn-ok:hover { background-color: #0842a0; color: #fff; }
</style>

<!-- Record Deposit Modal -->
<div class="modal fade material-modal" id="depositModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content border-0 shadow-lg">
            <form action="<?= BASE_URL ?>/admin/finance/deposit" method="POST" class="prevent-double-submit">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="modal-title-custom mb-0">Record Coffer Deposit</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="alert alert-success small rounded-4 border-0 mb-4">
                        Use this to record external funds being added to the business coffers.
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold small text-uppercase">Amount (₵)</label>
                        <input type="number" name="amount" class="form-control rounded-pill bg-light border-0 px-3 py-2 fs-5 fw-bold text-success" step="0.01" min="0" required placeholder="0.00" style="box-shadow: none;">
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted fw-bold small text-uppercase">Source / Purpose</label>
                        <textarea name="purpose" class="form-control rounded-4 bg-light border-0 px-3 py-2" rows="3" required placeholder="Source of funds or purpose of deposit" style="box-shadow: none;"></textarea>
                    </div>
                    <div class="d-flex justify-content-end align-items-center gap-2 mt-4">
                        <button type="button" class="btn btn-link btn-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-ok d-flex align-items-center gap-2" style="background-color: #198754;">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
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
        <div class="modal-content border-0 shadow-lg">
            <form action="<?= BASE_URL ?>/admin/finance/withdraw" method="POST" class="prevent-double-submit">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="modal-title-custom mb-0">Record Coffer Withdrawal</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="alert alert-info small rounded-4 border-0 mb-4">
                        Use this to record large fund movements from the business coffers.
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold small text-uppercase">Withdrawal Type</label>
                        <div class="d-flex gap-4">
                            <div class="form-check custom-radio">
                                <input class="form-check-input" type="radio" name="withdrawal_type" id="wt_normal" value="normal" checked onchange="toggleWithdrawalType()">
                                <label class="form-check-label text-dark fw-medium" for="wt_normal">Normal</label>
                            </div>
                            <div class="form-check custom-radio">
                                <input class="form-check-input" type="radio" name="withdrawal_type" id="wt_goods" value="goods" onchange="toggleWithdrawalType()">
                                <label class="form-check-label text-dark fw-medium" for="wt_goods">For Goods</label>
                            </div>
                        </div>
                    </div>
                    <div id="normal_amount_div" class="mb-3">
                        <label class="form-label text-muted fw-bold small text-uppercase">Amount (₵)</label>
                        <input type="number" name="amount" id="normal_amount" class="form-control rounded-pill bg-light border-0 px-3 py-2 fs-5 fw-bold text-danger" step="0.01" min="0" required placeholder="0.00" style="box-shadow: none;">
                    </div>
                    <div id="goods_amount_div" class="mb-3 d-none row g-2">
                        <div class="col-6">
                            <label class="form-label text-muted fw-bold small text-uppercase" style="font-size: 11px;">Goods (₵)</label>
                            <input type="number" name="goods_amount" id="goods_amount" class="form-control rounded-pill bg-light border-0 px-3 py-2 fw-bold text-danger" step="0.01" min="0" placeholder="0.00" style="box-shadow: none;">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted fw-bold small text-uppercase" style="font-size: 11px;">Transport (₵)</label>
                            <input type="number" name="transport_amount" id="transport_amount" class="form-control rounded-pill bg-light border-0 px-3 py-2 fw-bold text-danger" step="0.01" min="0" placeholder="0.00" style="box-shadow: none;">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted fw-bold small text-uppercase">Purpose / Description</label>
                        <textarea name="purpose" class="form-control rounded-4 bg-light border-0 px-3 py-2" rows="3" required placeholder="Description of withdrawal" style="box-shadow: none;"></textarea>
                    </div>
                    <div class="d-flex justify-content-end align-items-center gap-2 mt-4">
                        <button type="button" class="btn btn-link btn-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-ok d-flex align-items-center gap-2">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
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
        <div class="modal-content border-0 shadow-lg">
            <form action="<?= BASE_URL ?>/admin/finance/update" method="POST" class="prevent-double-submit">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="id" id="edit_tx_id">
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="modal-title-custom mb-0">Edit Transaction</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold small text-uppercase">Amount (₵)</label>
                        <input type="number" name="amount" id="edit_tx_amount" class="form-control rounded-pill bg-light border-0 px-3 py-2 fs-5 fw-bold" step="0.01" min="0" required style="box-shadow: none;">
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted fw-bold small text-uppercase">Purpose / Description</label>
                        <textarea name="purpose" id="edit_tx_purpose" class="form-control rounded-4 bg-light border-0 px-3 py-2" rows="3" required style="box-shadow: none;"></textarea>
                    </div>
                    <div class="d-flex justify-content-end align-items-center gap-2 mt-4">
                        <button type="button" class="btn btn-link btn-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-ok d-flex align-items-center gap-2" style="background-color: #198754;">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
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
        <div class="modal-content border-0 shadow-lg">
            <form action="<?= BASE_URL ?>/admin/finance/delete" method="POST" class="prevent-double-submit">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="id" id="delete_tx_id">
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <span class="material-symbols-outlined text-danger" style="font-size: 48px;">warning</span>
                    </div>
                    <h4 class="modal-title-custom mb-3">Delete Transaction?</h4>
                    <p class="text-muted mb-4">The money will be returned to the coffers. This action cannot be undone.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-link btn-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-ok d-flex align-items-center gap-2" style="background-color: #dc3545;">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span class="btn-text">Delete</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal data population
    const editModal = new bootstrap.Modal(document.getElementById('editTxModal'));
    document.querySelectorAll('.edit-tx-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_tx_id').value = this.dataset.id;
            document.getElementById('edit_tx_amount').value = this.dataset.amount;
            document.getElementById('edit_tx_purpose').value = this.dataset.purpose;
            editModal.show();
        });
    });

    // Delete Modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteTxModal'));
    document.querySelectorAll('.delete-tx-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('delete_tx_id').value = this.dataset.id;
            deleteModal.show();
        });
    });

    // Double submission prevention
    document.querySelectorAll('.prevent-double-submit').forEach(form => {
        form.addEventListener('submit', function() {
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
