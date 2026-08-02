<?php
$title = "Edit Expenditure";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3 mb-3">
            <h1 class="h3" style="color: #1f1f1f; font-weight: 400;">Edit Expenditure</h1>
            <a href="<?= BASE_URL ?>/expenditures" class="btn btn-light d-flex align-items-center gap-2 text-decoration-none" style="border-radius: 20px; font-weight: 500;">
                <span class="material-symbols-outlined fs-6">arrow_back</span> Back to List
            </a>
        </div>

        <style>
            .google-card {
                background: #fff;
                border-radius: 28px;
                overflow: hidden;
                margin-bottom: 24px;
                border: none;
                box-shadow: none;
            }
            .google-row {
                display: flex;
                align-items: center;
                padding: 24px 32px;
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
                margin-right: 24px;
                font-size: 24px;
            }
            .google-content {
                flex-grow: 1;
                display: flex;
                flex-direction: column;
            }
            .google-label {
                font-size: 16px;
                color: #1f1f1f;
                margin-bottom: 4px;
                font-weight: 500;
            }
            .google-input, .google-textarea, .google-select {
                border: none;
                outline: none;
                background: transparent;
                font-size: 15px;
                color: #5f6368;
                padding: 0;
                width: 100%;
            }
            .google-input:focus, .google-textarea:focus, .google-select:focus {
                color: #202124;
            }
            .google-textarea {
                resize: none;
                margin-top: 2px;
            }
            .google-select {
                appearance: none;
                background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%235f6368'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right center;
                background-size: 24px;
                padding-right: 32px;
                cursor: pointer;
            }
            .btn-save {
                background-color: #0b57d0;
                color: #fff;
                border-radius: 24px;
                padding: 10px 24px;
                font-weight: 500;
                border: none;
                transition: background-color 0.2s;
            }
            .btn-save:hover {
                background-color: #0842a0;
            }
        </style>

        <form action="<?= BASE_URL ?>/expenditures/edit?id=<?= $expenditure['id'] ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="google-card">
                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">calendar_month</span>
                    <div class="google-content">
                        <label class="google-label">Date</label>
                        <input type="date" name="date" class="google-input" value="<?= e($expenditure['date']) ?>" required>
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">category</span>
                    <div class="google-content">
                        <label class="google-label">Category</label>
                        <select name="category" class="google-select" required>
                            <?php 
                                $categories = ["Rent", "Electricity", "Water", "Salaries", "Maintenance", "Transport", "Others"];
                                foreach ($categories as $cat):
                            ?>
                                <option value="<?= $cat ?>" <?= ($expenditure['category'] == $cat) ? 'selected' : '' ?>><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">payments</span>
                    <div class="google-content">
                        <label class="google-label">Amount (₵)</label>
                        <input type="number" step="0.01" min="0" name="amount" class="google-input" value="<?= e($expenditure['amount']) ?>" required>
                    </div>
                </div>

                <div class="google-row" style="align-items: flex-start;">
                    <span class="material-symbols-outlined google-icon" style="margin-top: 2px;">description</span>
                    <div class="google-content">
                        <label class="google-label">Description / Purpose</label>
                        <textarea name="description" class="google-textarea" rows="2"><?= e($expenditure['description']) ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mb-5">
                <button type="submit" class="btn-save">Update Expenditure</button>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
