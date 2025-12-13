<?php
$title = "Sales History";
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Sales History</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= BASE_URL ?>/sales/create" class="btn btn-sm btn-primary d-flex align-items-center gap-2">
            <span class="fs-5">+</span> New Sale
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Invoice #</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Balance</th>
                        <th>Salesperson</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $sale): ?>
                    <?php 
                        $balance = $sale['total_amount'] - $sale['paid_amount']; 
                        $statusClass = match($sale['payment_status']) {
                            'paid' => 'bg-success',
                            'partial' => 'bg-warning text-dark',
                            'unpaid' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                    ?>
                    <tr>
                        <td><?php echo date('M j, Y H:i', strtotime($sale['created_at'])); ?></td>
                        <td><a href="<?= BASE_URL ?>/sales/view?id=<?php echo $sale['id']; ?>" class="fw-bold text-decoration-none">#<?php echo $sale['id']; ?></a></td>
                        <td><?php echo htmlspecialchars($sale['customer_name'] ?? ''); ?></td>
                        <td><span class="badge <?php echo $statusClass; ?> rounded-pill"><?php echo ucfirst($sale['payment_status']); ?></span></td>
                        <td class="text-end fw-bold">₵<?php echo number_format($sale['total_amount'], 2); ?></td>
                        <td class="text-end text-success">₵<?php echo number_format($sale['paid_amount'], 2); ?></td>
                        <td class="text-end text-danger"><?php echo ($balance > 0) ? '₵'.number_format($balance, 2) : '-'; ?></td>
                        <td><small><?php echo htmlspecialchars($sale['seller_name']); ?></small></td>
                        <td class="text-end">
                            <a href="<?= BASE_URL ?>/sales/view?id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-outline-secondary">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($sales)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No sales found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
