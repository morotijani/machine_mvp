<?php
$title = "Payment History";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Payment History: <?= e($debtor['name']) ?></h1>
            <a href="<?= BASE_URL ?>/debtors" class="btn btn-outline-secondary">Back to List</a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= e($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body bg-light rounded shadow-sm">
                <div class="row text-center">
                    <div class="col-md-4">
                        <small class="text-muted text-uppercase fw-bold">Total Debt</small>
                        <h4 class="mb-0">₵<?= number_format($debtor['total_amount'], 2) ?></h4>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted text-uppercase fw-bold text-success">Total Paid</small>
                        <h4 class="mb-0 text-success">₵<?= number_format($debtor['paid_amount'], 2) ?></h4>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted text-uppercase fw-bold text-danger">Remaining Balance</small>
                        <h4 class="mb-0 text-danger">₵<?= number_format($debtor['total_amount'] - $debtor['paid_amount'], 2) ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
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
                                <td><strong><?= date('M d, Y', strtotime($h['payment_date'])) ?></strong></td>
                                <td class="fw-bold text-success">₵<?= number_format($h['amount'], 2) ?></td>
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
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
