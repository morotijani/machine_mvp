<?php
$title = "Record Payment";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-6">
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
            .google-input {
                border: none;
                outline: none;
                background: transparent;
                font-size: 15px;
                color: #5f6368;
                padding: 0;
                width: 100%;
            }
            .google-input:focus {
                color: #202124;
            }
            .btn-confirm {
                background-color: #188038;
                color: #fff;
                border-radius: 24px;
                padding: 10px 24px;
                font-weight: 500;
                border: none;
                transition: background-color 0.2s;
            }
            .btn-confirm:hover {
                background-color: #137333;
            }
        </style>

        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3 mb-3">
            <h1 class="h3 mb-0" style="color: #1f1f1f; font-weight: 400;">Record Repayment</h1>
            <a href="<?= BASE_URL ?>/debtors" class="btn btn-light d-flex align-items-center gap-2 text-decoration-none" style="border-radius: 20px; font-weight: 500;">
                <span class="material-symbols-outlined fs-6">arrow_back</span> Cancel
            </a>
        </div>

        <form action="<?= BASE_URL ?>/debtors/payment?id=<?= $debtor['id'] ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="google-card">
                <div class="google-row" style="background-color: #f8f9fa;">
                    <span class="material-symbols-outlined google-icon text-primary">account_circle</span>
                    <div class="google-content">
                        <label class="google-label"><?= e($debtor['name']) ?></label>
                        <div style="font-size: 14px; color: #5f6368;">Outstanding Balance: <strong class="text-danger">₵<?= number_format($debtor['total_amount'] - $debtor['paid_amount'], 2) ?></strong></div>
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">calendar_today</span>
                    <div class="google-content">
                        <label class="google-label">Payment Date</label>
                        <input type="date" name="payment_date" class="google-input" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">payments</span>
                    <div class="google-content">
                        <label class="google-label">Amount to Pay (₵)</label>
                        <input type="number" step="0.01" min="0" name="amount" class="google-input" placeholder="0.00" max="<?= ($debtor['total_amount'] - $debtor['paid_amount']) ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mb-5">
                <button type="submit" class="btn-confirm">Confirm Payment</button>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
