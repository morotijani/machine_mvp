<?php
$title = "Increase Debt - " . e($debtor['name']);
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3">
            <h1 class="h2">Increase Debt</h1>
            <a href="<?= BASE_URL ?>/debtors" class="btn btn-outline-secondary btn-sm">
                <span class="material-symbols-outlined align-middle" style="font-size: 18px;">arrow_back</span>
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="mb-4">
                    <h5 class="fw-bold"><?= e($debtor['name']) ?></h5>
                    <p class="text-muted mb-1"><?= e($debtor['description']) ?></p>
                    <p class="mb-0">
                        <strong>Current Total Debt:</strong> <span class="text-danger">₵<?= number_format($debtor['total_amount'], 2) ?></span><br>
                        <strong>Paid:</strong> <span class="text-success">₵<?= number_format($debtor['paid_amount'], 2) ?></span><br>
                        <strong>Balance:</strong> <span class="text-danger fw-bold">₵<?= number_format($debtor['total_amount'] - $debtor['paid_amount'], 2) ?></span>
                    </p>
                </div>

                <form action="<?= BASE_URL ?>/debtors/increase?id=<?= $debtor['id'] ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <div class="mb-3">
                        <label class="form-label">Amount to Add</label>
                        <div class="input-group">
                            <span class="input-group-text">₵</span>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason/Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="e.g., Additional purchase, Interest, etc." required></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning">
                            <span class="material-symbols-outlined align-middle me-1" style="font-size: 18px;">add_circle</span>
                            Increase Debt
                        </button>
                        <a href="<?= BASE_URL ?>/debtors" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
