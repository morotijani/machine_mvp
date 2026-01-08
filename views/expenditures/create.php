<?php
$title = "Add Expenditure";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
            <h1 class="h2">Add Expenditure</h1>
            <a href="<?= BASE_URL ?>/expenditures" class="btn btn-outline-secondary">Back to List</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="<?= BASE_URL ?>/expenditures/create" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Date</label>
                        <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category</label>
                        <select name="category" class="form-select" required>
                            <option value="">Select Category</option>
                            <option value="Rent">Rent</option>
                            <option value="Electricity">Electricity</option>
                            <option value="Water">Water</option>
                            <option value="Salaries">Salaries</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Transport">Transport</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount (₵)</label>
                        <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description / Purpose</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Explain what this expenditure is for..."></textarea>
                    </div>
                    <div class="d-grid pt-3">
                        <button type="submit" class="btn btn-primary py-2 fw-bold">Save Expenditure</button>
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
