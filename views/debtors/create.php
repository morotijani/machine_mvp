<?php
$title = "Add Debtor";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3 mb-3">
            <h1 class="h3" style="color: #1f1f1f; font-weight: 400;">Add New Debtor</h1>
            <a href="<?= BASE_URL ?>/debtors" class="btn btn-light d-flex align-items-center gap-2 text-decoration-none" style="border-radius: 20px; font-weight: 500;">
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
            .google-input, .google-textarea {
                border: none;
                outline: none;
                background: transparent;
                font-size: 15px;
                color: #5f6368;
                padding: 0;
                width: 100%;
            }
            .google-input:focus, .google-textarea:focus {
                color: #202124;
            }
            .google-textarea {
                resize: none;
                margin-top: 2px;
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

        <form action="<?= BASE_URL ?>/debtors/create" method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="google-card">
                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">badge</span>
                    <div class="google-content">
                        <label class="google-label">Full Name</label>
                        <input type="text" name="name" class="google-input" placeholder="Enter full name of debtor" required>
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">call</span>
                    <div class="google-content">
                        <label class="google-label">Phone Number</label>
                        <input type="text" name="phone" class="google-input" placeholder="Optional phone number">
                    </div>
                </div>

                <div class="google-row">
                    <span class="material-symbols-outlined google-icon">payments</span>
                    <div class="google-content">
                        <label class="google-label">Total Debt Amount (₵)</label>
                        <input type="number" step="0.01" min="0" name="total_amount" class="google-input" placeholder="0.00" required>
                    </div>
                </div>

                <div class="google-row" style="align-items: flex-start;">
                    <span class="material-symbols-outlined google-icon" style="margin-top: 2px;">description</span>
                    <div class="google-content">
                        <label class="google-label">Description / Reason</label>
                        <textarea name="description" class="google-textarea" rows="2" placeholder="Explain the origin of this debt..."></textarea>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mb-5">
                <button type="submit" class="btn-save">Save Record</button>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
