<?php
$title = "Payment History";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <style>
            .google-summary-card {
                background: #fff;
                border-radius: 24px;
                overflow: hidden;
                margin-bottom: 24px;
                border: none;
                display: flex;
                flex-wrap: wrap;
            }
            .google-summary-item {
                flex: 1;
                padding: 24px 32px;
                border-right: 1px solid #e3e3e3;
                text-align: center;
                min-width: 200px;
            }
            .google-summary-item:last-child {
                border-right: none;
            }
            .google-table-card {
                background: #fff;
                border-radius: 24px;
                overflow: hidden;
                border: none;
                margin-bottom: 30px;
            }
            .google-table-card thead th {
                border-bottom: 1px solid #e3e3e3;
                background-color: #fff;
                color: #5f6368;
                font-weight: 500;
                padding: 16px 24px;
                font-size: 14px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .google-table-card tbody td {
                padding: 16px 24px;
                border-bottom: 1px solid #e3e3e3;
                color: #1f1f1f;
                vertical-align: middle;
            }
            .google-table-card tbody tr:last-child td {
                border-bottom: none;
            }
            .google-table-card tbody tr {
                transition: background-color 0.2s;
            }
            .google-table-card tbody tr:hover {
                background-color: #f8f9fa;
            }
            @media (max-width: 768px) {
                .google-summary-item {
                    border-right: none;
                    border-bottom: 1px solid #e3e3e3;
                }
                .google-summary-item:last-child {
                    border-bottom: none;
                }
            }
        </style>

        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3 mb-3">
            <h1 class="h3 mb-0" style="color: #1f1f1f; font-weight: 400;">Payment History: <?= e($debtor['name']) ?></h1>
            <a href="<?= BASE_URL ?>/debtors" class="btn btn-light d-flex align-items-center gap-2 text-decoration-none" style="border-radius: 20px; font-weight: 500;">
                <span class="material-symbols-outlined fs-6">arrow_back</span> Back to List
            </a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= e($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="google-summary-card">
            <div class="google-summary-item">
                <div style="color: #5f6368; font-size: 14px; text-transform: uppercase; font-weight: 500; margin-bottom: 8px;">Total Debt</div>
                <div style="color: #1f1f1f; font-size: 28px; font-weight: 400;">₵<?= number_format($debtor['total_amount'], 2) ?></div>
            </div>
            <div class="google-summary-item">
                <div style="color: #188038; font-size: 14px; text-transform: uppercase; font-weight: 500; margin-bottom: 8px;">Total Paid</div>
                <div style="color: #188038; font-size: 28px; font-weight: 400;">₵<?= number_format($debtor['paid_amount'], 2) ?></div>
            </div>
            <div class="google-summary-item">
                <div style="color: #d93025; font-size: 14px; text-transform: uppercase; font-weight: 500; margin-bottom: 8px;">Remaining Balance</div>
                <div style="color: #d93025; font-size: 28px; font-weight: 400;">₵<?= number_format($debtor['total_amount'] - $debtor['paid_amount'], 2) ?></div>
            </div>
        </div>

        <div class="google-table-card">
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <thead>
                        <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Recorded By</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history as $h): ?>
                            <tr>
                                <?php
                                    $isNegative = $h['amount'] < 0;
                                    $amountClass = $isNegative ? 'text-danger' : 'text-success';
                                    $displayAmount = ($isNegative ? '-' : '') . '₵' . number_format(abs($h['amount']), 2);
                                ?>
                                <td><strong><?= date('M d, Y', strtotime($h['payment_date'])) ?></strong></td>
                                <td class="fw-bold <?= $amountClass ?>"><?= $displayAmount ?></td>
                                <td><?= htmlspecialchars($h['username']) ?></td>
                                <td class="text-muted small"><?= date('H:i A', strtotime($h['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($history)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No payments recorded yet.</td></tr>
                            <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
