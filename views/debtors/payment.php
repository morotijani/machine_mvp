<?php
$title = "Record Payment";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Record Repayment</h1>
            <a href="<?= BASE_URL ?>/debtors" class="btn btn-outline-secondary">Cancel</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="alert alert-info">
                    <h6 class="alert-heading fw-bold">Debtor: <?= htmlspecialchars($debtor['name']) ?></h6>
                    <p class="mb-0">Outstanding Balance: <strong>₵<?= number_format($debtor['total_amount'] - $debtor['paid_amount'], 2) ?></strong></p>
                </div>

                <form action="<?= BASE_URL ?>/debtors/payment?id=<?= $debtor['id'] ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount to Pay (₵)</label>
                        <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" max="<?= ($debtor['total_amount'] - $debtor['paid_amount']) ?>" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success py-2 fw-bold">Confirm Payment</button>
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
