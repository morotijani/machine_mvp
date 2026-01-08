<?php
$title = "Edit Expenditure";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
            <h1 class="h2">Edit Expenditure</h1>
            <a href="<?= BASE_URL ?>/expenditures" class="btn btn-outline-secondary">Back to List</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="<?= BASE_URL ?>/expenditures/edit?id=<?= $expenditure['id'] ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Date</label>
                        <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($expenditure['date']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category</label>
                        <select name="category" class="form-select" required>
                            <?php 
                                $categories = ["Rent", "Electricity", "Water", "Salaries", "Maintenance", "Transport", "Others"];
                                foreach ($categories as $cat):
                            ?>
                                <option value="<?= $cat ?>" <?= ($expenditure['category'] == $cat) ? 'selected' : '' ?>><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount (₵)</label>
                        <input type="number" step="0.01" name="amount" class="form-control" value="<?= htmlspecialchars($expenditure['amount']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description / Purpose</label>
                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($expenditure['description']) ?></textarea>
                    </div>
                    <div class="d-grid pt-3">
                        <button type="submit" class="btn btn-primary py-2 fw-bold">Update Expenditure</button>
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
