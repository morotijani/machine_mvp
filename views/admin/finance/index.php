<?php
$title = "Finance & Coffers";
ob_start();
?>
<div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
    <h1 class="h2">Finance & Coffers Management</h1>
    <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#withdrawModal">
        <span class="material-symbols-outlined">payments</span> Record Withdrawal
    </button>
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
                <div class="text-muted small mt-2">Available liquid cash (Paid - Exp - Wdr)</div>
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
                                <th>Amount</th>
                                <th>Purpose / Details</th>
                                <th>Recorded By</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $tx): ?>
                            <tr>
                                <td><?= date('M j, Y H:i', strtotime($tx['created_at'])) ?></td>
                                <td class="fw-bold text-danger">-₵<?= number_format($tx['amount'], 2) ?></td>
                                <td><?= e($tx['purpose']) ?></td>
                                <td><?= e($tx['recorder_name']) ?></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary edit-tx-btn" 
                                                data-id="<?= $tx['id'] ?>" 
                                                data-amount="<?= $tx['amount'] ?>" 
                                                data-purpose="<?= e($tx['purpose']) ?>">
                                            <span class="material-symbols-outlined" style="font-size: 18px;">edit</span>
                                        </button>
                                        <form action="<?= BASE_URL ?>/admin/finance/delete" method="POST" onsubmit="return confirm('Delete this transaction? The money will be returned to the coffers.')" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                            <input type="hidden" name="id" value="<?= $tx['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No coffer transactions recorded.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Record Withdrawal Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>/admin/finance/withdraw" method="POST" class="prevent-double-submit">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Record Coffer Withdrawal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info small">
                        Use this to record large fund movements from the business coffers.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount (₵)</label>
                        <input type="number" name="amount" class="form-control form-control-lg" step="0.01" required placeholder="0.00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Purpose / Description</label>
                        <textarea name="purpose" class="form-control" rows="3" required placeholder="Description of withdrawal"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span class="btn-text">Confirm Withdrawal</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Withdrawal Modal -->
<div class="modal fade" id="editTxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>/admin/finance/update" method="POST" class="prevent-double-submit">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="id" id="edit_tx_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount (₵)</label>
                        <input type="number" name="amount" id="edit_tx_amount" class="form-control form-control-lg" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Purpose / Description</label>
                        <textarea name="purpose" id="edit_tx_purpose" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success d-flex align-items-center gap-2">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span class="btn-text">Save Changes</span>
                    </button>
                </div>
            </form>
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

    // Double submission prevention
    document.querySelectorAll('.prevent-double-submit').forEach(form => {
        form.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            const spinner = btn.querySelector('.spinner-border');
            const btnText = btn.querySelector('.btn-text');
            
            btn.disabled = true;
            spinner.classList.remove('d-none');
            if (btnText) btnText.innerText = 'Processing...';
        });
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/main.php';
?>
