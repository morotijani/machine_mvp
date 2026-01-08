<?php
$title = "Add Debtor";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Add New Debtor</h1>
            <a href="<?= BASE_URL ?>/debtors" class="btn btn-outline-secondary">Back to List</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="<?= BASE_URL ?>/debtors/create" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter full name of debtor" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="Optional phone number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Total Debt Amount (₵)</label>
                        <input type="number" step="0.01" name="total_amount" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description / Reason</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Explain the origin of this debt..."></textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-bold">Save Debtor Record</button>
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
